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

class PageScheduledStaging extends SiteTreeExtension {
	
	private static $db = array(
		'ScheduledPublish' => 'SS_Datetime',
		'ScheduledUnpublish' => 'SS_Datetime',
		'PublicTimestamp' => 'SS_Datetime'
	);
	
	public function updateCMSFields(FieldList $fields) {
		$this->owner->autoScaffoldExtensionFormFields($fields,
			'Root.PageScheduledStaging', get_class($this), $this->owner, null);
	}
	
	public function updateFieldLabels(&$labels) {
		$labels['Root_PageScheduledStaging'] = _t('PageScheduledStaging.Root_PageScheduledStaging',
			'Scheduled staging');
		
		$labels['ScheduledPublish'] = _t('PageScheduledStaging.ScheduledPublish', 'Scheduled publish');
		$labels['ScheduledUnpublish'] = _t('PageScheduledStaging.ScheduledUnpublish', 'Scheduled unpublish');
		$labels['PublicTimestamp'] = _t('PageScheduledStaging.PublicTimestamp', 'Public timestamp');
	}
	
	public function onAfterWrite() {
		$publisher = Member::currentUser();
		$page = $this->owner;
		
		ScheduledJob::register('StagePageTask', null, $publisher, $page, 'doPublish');
		ScheduledJob::register('StagePageTask', null, $publisher, $page, 'doUnpublish');
	}
	
}

}
