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

class PageSummary extends SiteTreeExtension {
	
	private static $db = array(
		'SummaryTitle' => 'Text',
		'SummaryContent' => 'HTMLText'
	);
	
	private static $has_one = array(
		'SummaryThumbnail' => 'Image'
	);
	
	private static $summary_template_fields = array(
		'PageSummaryTitle' => array(
			'SummaryTitle',
			'Title'
		),
		'PageSummaryContent' => array(
			'SummaryContent',
			'Content'
		),
		'PageSummaryThumbnail' => array(
			'SummaryThumbnailID'
		)
	);
	
	public function updateCMSFields(FieldList $fields) {
		$fieldTransformation = new FormTransformation_SpecificFields(array(
			'SummaryTitle' => 'TextField'
		));
		$this->owner->autoScaffoldExtensionFormFields($fields,
			'Root.PageSummary', get_class($this), $this->owner,
			$fieldTransformation);
	}
	
	public function updateFieldLabels(&$labels) {
		$labels['Root_PageSummary'] = _t('PageSummary.Root_PageSummary', 'Summary');
		
		$labels['SummaryTitle'] = _t('PageSummary.SummaryTitle', 'Title');
		$labels['SummaryContent'] = _t('PageSummary.SummaryContent', 'Content');
		$labels['SummaryThumbnail'] = $labels['SummaryThumbnailID']
			= _t('PageSummary.SummaryThumbnail', 'Thumbnail');
	}
	
	public function Summary($showThumbnail = true, $showLabels = true) {
		$templateFields = $this->owner->config()->summary_template_fields;
		$templateValues = array(
			'ShowThumbnail' => (bool)$showThumbnail,
			'ShowPageLabels' => (bool)$showLabels
		);
		
		foreach ($templateFields as $key => $fields) {
			foreach ($fields as $name) {
				$field = $this->owner->dbObject($name);
				
				if ($field instanceof StringField) {
					if (strip_tags($field->RAW()) != '')
						$templateValues[$key] = $field;
				} else if ($field instanceof ForeignKey) {
					$relation = preg_replace('/ID$/', '', $name);
					$relation = $this->owner->$relation();
					if ($relation instanceof Image && $relation->exists())
						$templateValues[$key] = $relation;
				} else {
					throw new Exception(sprintf("%s of type %s is not a supported summary field"
							. " - only Image and StringField subclasses are supported",
						$name, get_class($field)));
				}
				
				if (isset($templateValues[$key]))
					break;
			}
		}
		
		return $this->owner->renderWith('PageSummary', $templateValues);
	}
	
}

}
