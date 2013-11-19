<?php

/**
 * Combine the best parts of silverstripe-autocomplete by
 * Damian Mooyman (tractorcow) and silverstripe-tagfield
 * by Ingo Schommer (chillu).
 * 
 * @see https://github.com/tractorcow
 * @see https://github.com/tractorcow/silverstripe-autocomplete/
 * @see https://github.com/chillu/
 * @see https://github.com/chillu/silverstripe-tagfield/
 * 
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

class SimpleTagField extends TextField {
	
	private static $min_search_length = 2;
	
	private static $allowed_actions = array(
		'Suggest'
	);
	
	protected $suggestURL = '';
	protected $ownerClass = '';
	protected $sourceField = '';
	protected $sourceFilter = '';
	protected $sourceLimit = 32;
	
	function __construct($name, $title = null, $value = null,
			$ownerClass = null, $sourceField = 'Title',
			$maxLength = null, $form = null) {
		$this->ownerClass = $ownerClass;
		$this->sourceField = $sourceField;
		parent::__construct($name, $title, $value, $maxLength, $form);
	}
	
	public function setSuggestURL(string $URL) {
		$this->suggestURL = $URL;
	}
	
	public function getSuggestURL() {
		if (!empty($this->suggestURL))
			return $this->suggestURL;
		return parse_url($this->Link(), PHP_URL_PATH) . '/Suggest';
	}
	
	public function setSourceField(string $field) {
		$this->sourceFilter = $field;
	}
	
	public function getSourceField() {
		return $this->sourceField;
	}
	
	public function setSourceFilter(string $filter) {
		$this->sourceFilter = $filter;
	}
	
	public function getSourceFilter() {
		return $this->sourceFilter;
	}
	
	public function setSourceLimit(int $limit) {
		$this->sourceLimit = $limit;
	}
	
	public function getSourceLimit() {
		return $this->sourceLimit;
	}
	
	function setValue($value, $obj = null) {
		if (
			is_object($obj) &&
			$obj instanceof DataObject &&
			$obj->many_many($this->getName())
		) {
			$tags = $obj->{$this->getName()}();
			$tags = array_values($tags->map('ID', $this->getSourceField())->toArray());
			$this->value = implode(', ', $tags);
		} else {
			parent::setValue($value, $obj);
		}
	}
	
	function getAttributes() {
		return array_merge(
			parent::getAttributes(), array(
				'data-source' => $this->getSuggestURL(),
				'data-min-length' => $this->config()->min_search_length,
				'autocomplete' => 'off'
			)
		);
	}
	
	function Type() {
		return parent::Type() . ' text';
	}
	
	function Field($properties = array()) {
		Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
		Requirements::javascript(PATCHWORK_DIR . '/javascript/SimpleTagField.js');
		
		return parent::Field($properties);
	}

	/**
	 * The class which owns the tags in question.
	 * 
	 * @return null|string
	 */
	protected function determineOwnerClass() {
		if ($ownerClass = $this->ownerClass)
			return $ownerClass;
		$form = $this->getForm();
		if (!$form)
			return null;
		$record = $form->getRecord();
		if (!$record)
			return null;
		return $record->ClassName;
	}
	
	protected function determineTagClass() {
		$ownerClass = $this->determineOwnerClass();
		$ownerRelation = $this->getName();
		$tagManyMany = $ownerClass::create()->many_many();
		if (!$tagManyMany) {
			throw new Exception("can not find a many_many relation"
				. " named $ownerRelation on $ownerClass");
		}
		return $tagManyMany[$this->getName()];
	}
	
	public function saveInto(DataObjectInterface $record) {
		// This should not happen, since it is recommended to add the 
		// tag field after the DataObject has been saved once.
		if (!$record->isInDB())
			$record->write();
		
		$tags = trim($this->value, ", \t\n\r");
		$tags = preg_split('/\s*,\s*/', $tags);
		array_walk($tags, function (&$item) {
			$item = trim($item);
		});
		
		$recordRelationName = $this->getName();
		$recordTags = $record->$recordRelationName();
		
		$tagClass = $this->determineTagClass();
		$tagBaseClass = ClassInfo::baseDataClass($tagClass);
		
		$sourceField = $this->sourceField;
		
		$newTags = array();
		
		foreach (array_filter($tags) as $rawTag) {
			$realTag = $tagClass::get()->where(sprintf("\"%s\".\"%s\" = '%s'",
				$tagBaseClass, $sourceField, Convert::raw2sql($rawTag)))->First();
			if (!$realTag) {
				$realTag = $tagClass::create();
				$realTag->$sourceField = $rawTag;
				$realTag->write();
			}
			$newTags[] = $realTag->ID;
		}
		$recordTags->setByIdList($newTags);
	}
	
	/**
	 * Handle a request for an Autocomplete list.
	 */
	public function Suggest(HTTPRequest $request) {
		$tagClass = $this->determineTagClass();
		if (!$tagClass)
			return '';
		
		$sourceField = $this->getSourceField();
		$sourceFilter = $this->getSourceFilter();
		$searchTerm = Convert::raw2sql($request->getVar('term'));
		$searchLimit = $this->getSourceLimit();
		
		$tags = $tagClass::get()
			->where("\"$sourceField\" LIKE '%$searchTerm%'")
			->sort($sourceField)
			->limit($searchLimit);
		
		if (trim($sourceFilter))
			$tags->where($sourceFilter);
		
		$items = array();
		foreach ($tags as $tag) {
			$value = $tag->$sourceField;
			if (!in_array($value, $items))
				$items[] = $value;
		}
		return json_encode($items);
	}
	
}
