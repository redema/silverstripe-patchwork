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

class SiteFooter extends DataExtension {
	
	private static $db = array(
		'FooterContact' => 'Text',
		'FooterWildcard' => 'Text'
	);
	
	private static $has_one = array(
		'FooterMenuParent' => 'Page'
	);
	
	public function updateCMSFields(FieldList $fields) {
		$fieldTransformation = new FormTransformation_SpecificFields(array(
			'FooterMenuParentID' => function ($field) {
				$pages = Page::get()
					->filter('ParentID', 0)
					->exclude('ClassName', 'ErrorPage');
				$field->setSource($pages->map('ID', 'Title'));
				return $field;
			}
		));
		$this->owner->autoScaffoldExtensionFormFields($fields,
			'Root.SiteFooter', get_class($this), $this->owner,
			$fieldTransformation);
	}
	
	public function updateFieldLabels(&$labels) {
		$labels['Root_SiteFooter'] = _t('SiteFooter.Root_SiteFooter', 'Footer');
		
		$labels['FooterContact'] = _t('SiteFooter.FooterContact', 'Contact');
		$labels['FooterWildcard'] = _t('SiteFooter.FooterWildcard', 'Wildcard');
		
		$labels['FooterMenuParent'] = $labels['FooterMenuParentID']
			=_t('SiteFooter.FooterMenuParent', 'Menu parent');
	}
	
}

}
