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

/**
 * A less clever version of RedirectorPage. It will do
 * exactly as it is told.
 */
class LiteralRedirectorPage extends Page {
	
	private static $db = array(
		'Redirect' => 'Text',
		'RedirectCode' => "Enum(Array(
			'301',
			'302',
			'303',
			'304',
			'305',
			'307'
		), '301')"
	);
	
	public function subPagesToCache() {
		return array();
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$fields->addFieldToTab('Root.Main', new TextField('Redirect',
			$this->fieldLabel('Redirect')), 'Content');
		$fields->addFieldToTab('Root.Main', $this->dbObject('RedirectCode')
			->scaffoldFormField($this->fieldLabel('RedirectCode')), 'Content');
		
		$fields->removeByName('Content');
		
		return $fields;
	}
	
	public function fieldLabels($includerelations = true) {
		$labels = parent::fieldLabels($includerelations);
		
		$labels['Redirect'] = _t('LiteralRedirectorPage.Redirect', 'Redirect');
		$labels['RedirectCode'] = _t('LiteralRedirectorPage.RedirectCode', 'Redirect code');
		
		if ($includerelations) {
		}
		
		return $labels;
	}
	
}

class LiteralRedirectorPage_Controller extends Page_Controller {
	
	public function init() {
		parent::init();
		if ($this->data()->Redirect) {
			return $this->redirect($this->data()->Redirect,
				$this->data()->RedirectCode);
		} else {
			return $this->httpError(404);
		}
	}
}

}
