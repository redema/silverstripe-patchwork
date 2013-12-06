<?php

/**
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * 
 * * Neither the name of Redema, nor the names of its contributors may be used
 *   to endorse or promote products derived from this software without specific
 *   prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * The description of a deferred build task scheduled to
 * run at some point.
 */
class ScheduledJob extends DataObject {
	
	private static $db = array(
		'Task' => 'Text',
		'GetParams' => 'Text',
		'PostParams' => 'Text',
		'Uniqid' => 'Text',
		'Scheduled' => 'SS_Datetime',
		'Completed' => 'SS_Datetime',
		'Failed' => 'SS_Datetime',
		'Repeats' => 'Int',
		'Reschedule' => 'Int'
	);
	
	private static $has_one = array(
		'Owner' => 'Member'
	);
	
	private static $defaults = array(
		'Repeats' => '0',
		'Reschedule' => '0'
	);
	
	public static $searchable_fields = array(
		'ID' => array('filter' => 'ExactMatchFilter'),
		'Task',
		'GetParams',
		'PostParams',
		'Scheduled',
		'Completed',
		'Failed',
		'Owner.ID'
	);
	
	public static $summary_fields = array(
		'ID',
		'Task',
		'Scheduled',
		'Completed',
		'Failed',
		'Repeats',
		'Reschedule',
		'Owner.Email'
	);
	
	/**
	 * Register a new scheduled job.
	 * 
	 * @param string $DeferrableBuildTask
	 * @param null|array $ScheduledJobFields
	 * @param null|Member $Owner
	 * @param mixed $arg1..N
	 * 
	 * @return ScheduledJob The job will be written to the database
	 * only if the DeferrableBuildTask decided to schedule it.
	 */
	public static function register(/* string $DeferrableBuildTask,
			null|array $ScheduledJobFields, null|Member $Owner, ... */) {
		$params = func_get_args();
		$deferrable = array_shift($params);
		$fields = array_shift($params);
		
		if (!is_string($deferrable) || !class_exists($deferrable) ||
				!in_array('DeferrableBuildTask', ClassInfo::ancestry($deferrable))) {
			throw new \InvalidArgumentException('expected the name of a DeferrableBuildTask'
				. ' subclass as the first argument');
		}
		if (!(is_null($fields) || is_array($fields))) {
			throw new \InvalidArgumentException('expected field values for the scheduled'
				. ' job or null as the second argument');
		}
		if (count($params) < 1 || !(is_null($params[0]) || (is_object($params[0]) &&
				$params[0] instanceof Member))) {
			throw new \InvalidArgumentException('expected the member who should own'
				. ' the scheduled job or null as the third argument');
		}
		
		$job = new ScheduledJob();
		$job->Task = $deferrable;
		if (is_object($params[0]) && $params[0] instanceof Member) {
			$job->OwnerID = $params[0]->ID;
		}
		if (is_array($fields) && count($fields)) {
			foreach ($fields as $field => $value)
				$job->$field = $value;
		}
		
		array_unshift($params, $job);
		
		return call_user_func_array(array($deferrable, 'schedule'), $params);
	}
	
	public function setTaskParams($name, array $params) {
		$this->setField($name, http_build_query($params));
	}
	
	public function getTaskParams($name) {
		$params = array();
		parse_str($this->getField($name), $params);
		return $params;
	}
	
	public function setUniqid() {
		$this->setField('Uniqid', sha1(var_export(func_get_args(), true)));
	}
	
	public function deleteDuplicates() {
		$duplicates = ScheduledJob::get()->filter('Uniqid', $this->Uniqid);
		foreach ($duplicates as $duplicate)
			$duplicate->delete();
	}
	
	public function validTask() {
		return (
			$this->ID &&
			$this->Task &&
			class_exists($this->Task) &&
			in_array('DeferrableBuildTask', ClassInfo::ancestry($this->Task))
		);
	}
	
	public function date($timestamp = 0) {
		return date('Y-m-d H:i:s', $timestamp > 0?
			$timestamp: time());
	}
	
	protected function complete($response) {
		if ($this->Reschedule) {
			$this->Repeats = $this->Repeats > 0?
				$this->Repeats - 1: $this->Repeats;
			if ($this->Repeats) {
				$this->Scheduled = $this->date(strtotime($this->$this->Scheduled)
					+ $this->Reschedule);
			} else {
				$this->Completed = $this->date();
			}
		} else {
			$this->Completed = $this->date();
		}
		$this->extend('scheduledJobComplete', $response);
		$this->write();
		return true;
	}
	
	protected function fail($response) {
		$this->Failed = $this->date();
		$this->extend('scheduledJobFail', $response);
		$this->write();
		return false;
	}
	
	public function run() {
		if (!$this->validTask()) {
			return $this->fail(null);
		}
		
		$post = $this->getTaskParams('PostParams');
		$get = sprintf('%s/%s/%s?%s', BASE_URL, 'dev/tasks', $this->Task,
			http_build_query($this->getTaskParams('GetParams')));
		
		if ($this->Owner()->ID)
			$this->Owner()->logIn();
		
		$method = empty($post)? 'GET': 'POST';
		$session = new Session(Session::get_all());
		
		$this->extend('onBeforeRun');
		$response = Director::test($get, $post, $session, $method,
			null, null, $_COOKIE);
		$this->extend('onAfterRun');
		
		if ($this->Owner()->ID)
			$this->Owner()->logOut();
		
		return $response->isError()?
			$this->fail($response): $this->complete($response);
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$fieldTransformation = new FormTransformation_SpecificFields(array(
			'Task' => 'TextField',
			'GetParams' => 'TextField',
			'PostParams' => 'TextField',
			'Uniqid' => 'TextField'
		));
		$replaceField = function (FieldList $fields, $tab, FormField $field) {
			$fields->replaceField($field->getName(), $field);
		};
		$this->autoScaffoldFormFields($fields, null, get_class($this),
			$this, $fieldTransformation, $replaceField);
		return $fields;
	}
	
	public function fieldLabels($includerelations = true) {
		$labels = parent::fieldLabels($includerelations);
		
		$labels['Task'] = _t('ScheduledJob.Task', 'Task');
		$labels['GetParams'] = _t('ScheduledJob.GetParams', 'Get params');
		$labels['PostParams'] = _t('ScheduledJob.PostParams', 'Post params');
		$labels['Uniqid'] = _t('ScheduledJob.Uniqid', 'Uniqid');
		$labels['Scheduled'] = _t('ScheduledJob.Scheduled', 'Scheduled');
		$labels['Completed'] = _t('ScheduledJob.Completed', 'Completed');
		$labels['Failed'] = _t('ScheduledJob.Failed', 'Failed');
		$labels['Repeats'] = _t('ScheduledJob.Repeats', 'Repeats');
		$labels['Reschedule'] = _t('ScheduledJob.Reschedule', 'Reschedule');
		
		if ($includerelations) {
			$labels['Owner'] = $labels['OwnerID']
				= _t('ScheduledJob.Owner', 'Owner');
		}
		
		return $labels;
	}
	
}
