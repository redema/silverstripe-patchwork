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

class PageLabel extends DataObject {
	
	private static $db = array(
		'Title' => 'Text',
		'TemplateName' => 'Text',
		'URLName' => 'Text'
	);
	
	public static $searchable_fields = array(
		'ID' => array('filter' => 'ExactMatchFilter'),
		'Title',
		'TemplateName',
		'URLName'
	);
	
	public static $summary_fields = array(
		'ID',
		'Title',
		'TemplateName',
		'URLName'
	);
	
	/**
	 * Get all labels of the given type for the given pages.
	 * 
	 * @see PageCategory::categories_from()
	 * @see PageTag::tags_from()
	 */
	public static function labels_from(string $class, string $manyManyTable, DataList $pages) {
		$pageIDs = $pages->column('ID');
		if (count($pageIDs)) {
			$pageIDs = implode(', ', $pageIDs);
			$joinOn = <<<INLINE_SQL
"PageLabel"."ID" = "$manyManyTable"."{$class}ID"
	AND ("$manyManyTable"."PageID" IN ($pageIDs))
INLINE_SQL;
			$labels = $class::get()->innerJoin($manyManyTable, $joinOn);
		} else {
			$labels = $class::get()->where('"PageLabel"."ID" < 1');
		}
		return $labels;
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$fieldTransformation = new FormTransformation_SpecificFields(array(
			'Title' => 'TextField',
			'TemplateName' => 'TextField',
			'URLName' => function (FormField $field) {
				return $field->performDisabledTransformation();
			}
		));
		$replaceField = function (FieldList $fields, $tab, FormField $field) {
			$fields->replaceField($field->getName(), $field);
		};
		$this->autoScaffoldFormFields($fields, null, get_class($this),
			$this, $fieldTransformation, $replaceField);
		
		if ($this->ID && $this->hasMethod('Pages')) {
			$pages = $this->Pages()->map('ID', 'Title')->values();
			$pages = implode('</li><li>', Convert::raw2xml($pages));
			$pages = "<ul><li>$pages</li></ul>";
			$pagesReadonlyField = new LiteralField('Pages', $pages);
			$fields->replaceField('Pages', $pagesReadonlyField);
		} else {
			$fields->removeByName('Pages');
		}
		
		return $fields;
	}
	
	public function fieldLabels($includerelations = true) {
		$labels = parent::fieldLabels($includerelations);
		
		$labels['Title'] = _t('PageLabel.Title', 'Title');
		$labels['TemplateName'] = _t('PageLabel.TemplateName', 'Template name');
		$labels['URLName'] = _t('PageLabel.URLName', 'URL name');
		
		if ($includerelations) {
		}
		
		return $labels;
	}
	
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		
		$this->URLName = Convert::raw2url($this->Title);
	}
	
	public function onAfterWrite() {
		parent::onAfterWrite();
		if ($this->hasMethod('Pages') && count($this->Pages())) {
			ScheduledJob::register('UpdateMetaLabelsTask', null, null);
		}
	}
	
	public function onAfterDelete() {
		parent::onAfterDelete();
		ScheduledJob::register('UpdateMetaLabelsTask', null, null);
	}
	
	/**
	 * @param int $ID
	 * 
	 * @return string
	 */
	public function SearchLink($forTemplate = true) {
		$args = array(
			'PageCategory' => array(),
			'PageTag' => array()
		);
		if (isset($args[$this->ClassName])) {
			$args[$this->ClassName][$this->ID] = $this->ID;
		}
		return PageSiteSearch::build_link('', '',
			$args['PageCategory'], $args['PageTag'], $forTemplate);
	}
	
}

}
