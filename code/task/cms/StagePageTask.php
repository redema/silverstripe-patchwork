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

if (class_exists('SiteTree')) {

class StagePageTask extends DeferrableBuildTask {
	
	protected $title = 'Publish/Unpublish a page';
	protected $description = 'Specify what will happen using ?PageID=N&Method=(doPublish|doUnpublish).';
	
	public static function schedule() {
		extract(Funky::exractable_args(func_get_args(), array(
			'ScheduledJob $scheduledJob',
			'$publisher',
			'$page',
			'$method'
		)));
		
		$scheduledJob->setUniqid($page->ID, $method);
		$scheduledJob->deleteDuplicates();
		$methodFields = array(
			'doPublish' => 'ScheduledPublish',
			'doUnpublish' => 'ScheduledUnpublish'
		);
		if (!isset($methodFields[$method])) {
			throw new \InvalidArgumentException("unsupported staging method $method");
		}
		
		$methodField = $methodFields[$method];
		if ($page->$methodField > date('Y-m-d H:i:s')) {
			$scheduledJob->Scheduled = $page->$methodField;
			$scheduledJob->setTaskParams('GetParams', array(
				'PageID' => $page->ID,
				'Method' => $method
			));
			$scheduledJob->write();
		}
		
		return $scheduledJob;
	}
	
	public function run($request) {
		$pageID = $request->getVar('PageID');
		$stageMethod = $request->getVar('Method');
		$stageMethods = array(
			'doPublish',
			'doUnpublish'
		);
		
		if (!is_numeric($pageID) || !in_array($stageMethod, $stageMethods)) {
			return $this->httpError(400);
		}
		
		if (($page = Versioned::get_one_by_stage('Page', 'Stage', sprintf(
				'"SiteTree"."ID" = %d', (int)$pageID)))) {
			$page->$stageMethod();
		}
	}
	
}

}
