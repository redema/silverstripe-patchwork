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
 * This is a highly experimental attempt to be a little clever.
 * 
 * FIXME: Write tests!
 */
class PageAggregate extends Page {
	
	const SEARCH_RESULT_SORT_RELEVANCE = 'Relevance';
	const SEARCH_RESULT_SORT_RANDOM = 'Random';
	const SEARCH_RESULT_SORT_TIMESTAMP = 'Timestamp';
	const SEARCH_RESULT_SORT_ALPHABETICAL = 'Alphabetical';
	const SEARCH_RESULT_SORT_SITETREE = 'SiteTree';
	
	private static $db = array(
		'SearchNeedle' => 'Text',
		'SearchResultLimit' => 'Int',
		'SearchResultPerPage' => 'Int',
		'SearchResultSort' => "Enum(Array(
			'Relevance',
			'Random',
			'Timestamp',
			'Alphabetical',
			'SiteTree'
		), 'Relevance')",
		'SearchExcludePageAggregates' => 'Boolean',
		'SearchExcludeErrorPages' => 'Boolean'
	);
	
	private static $has_one = array(
	);
	
	private static $defaults = array(
		'SearchResultPerPage' => '20',
		'SearchExcludePageAggregates' => '1',
		'SearchExcludeErrorPages' => '1'
	);
	
	private static $weight_per_label = 10.0;
	
	protected $searchParams = array(
		'Needle' => array(
			'field' => 'SearchNeedle',
			'value' => null,
			'changed' => false
		),
		'Categories' => array(
			'field' => 'Categories',
			'value' => null,
			'changed' => false
		),
		'Tags' => array(
			'field' => 'Tags',
			'value' => null,
			'changed' => false
		)
	);
	
	public function setSearchParam($name, $value) {
		if (!isset($this->searchParams[$name])) {
			throw new \InvalidArgumentException("search param \"$name\" does not exist");
		} else if (!empty($value)) {
			$this->searchParams[$name]['value'] = $value;
			$this->searchParams[$name]['changed'] = true;
		}
	}
	
	protected function searchParamValueCategories() {
		return $this->Categories()->column('ID');
	}
	
	protected function searchParamValueTags() {
		return $this->Tags()->column('ID');
	}
	
	public function getSearchParam($name, $originalSearchParam = false) {
		if (isset($this->searchParams[$name])) {
			$param = $this->searchParams[$name];
			$method = "searchParamValue{$param['field']}";
			if (!$originalSearchParam && !empty($param['value']))
				return $param['value'];
			
			$value = $this->hasMethod($method)?
				$this->$method(): $this->{$param['field']};
			
			if (!$originalSearchParam)
				$this->searchParams[$name]['value'] = $value;
			
			return $value;
		}
		return false;
	}
	
	public function getSearchParamMetadata($name, $key) {
		if (!isset($this->searchParams[$name]))
			throw new \InvalidArgumentException("search param \"$name\" does not exist");
		else if (!isset($this->searchParams[$name][$key]))
			throw new \InvalidArgumentException("search param key \"$key\" does not exist");
		else
			return $this->searchParams[$name][$key];
	}
	
	public function getSearchParamNames() {
		return array_keys($this->searchParams);
	}
	
	public function resetSearchParams() {
		array_walk($this->searchParams, function (&$param, $name) {
			$param['value'] = null;
		});
	}
	
	protected $findPageIDsCache = array();
	
	/**
	 * Get an unsorted list of page IDs matching the given search
	 * params. The keys in the returned array are the page IDs,
	 * the values are "relevance" values.
	 * 
	 * @param string $needle
	 * @param array $categories
	 * @param array $tags
	 * @param boolean $cache
	 * 
	 * @return array
	 */
	public function findPageIDs($needle, array $categories, array $tags, $cache = true) {
		$safeNeedle = Convert::raw2sql($needle);
		
		$pageQuery = new SQLQuery();
		
		$pageQuery->setFrom('SiteTree');
		$pageQuery->setSelect('SiteTree.ID');
		
		// Search for $needle in the $haystackFields for Pages. Each field
		// get a primitive weight value, where a higher weight should
		// mean "more relevant page" (http://stackoverflow.com/a/5427534).
		// The $needle must be found in at least one of the $haystackFields
		// in order for the page in question to be included. $haystackFields
		// will also determine sort order, it is assumed that they are
		// ordered as "most important" to "least important".
		if (trim($needle)) {
			$haystackFilters = array();
			$haystackFields = array(
				'Content',
				'Title',
				'MenuTitle'
			);
			$this->extend('updateFindPageIDsHaystackFields', $haystackFields);
			foreach ($haystackFields as $haystackField) {
				$haystackAlias = "{$haystackField}Weight";
				$haystackWeight = <<<INLINE_SQL
(LENGTH("$haystackField") - LENGTH(REPLACE("$haystackField", '$safeNeedle', '')))
	/ LENGTH('$safeNeedle')
INLINE_SQL;
				$pageQuery->selectField($haystackWeight, $haystackAlias);
				$pageQuery->addOrderBy($haystackField, 'DESC');
				$haystackFilters[] = "\"$haystackAlias\" > 0";
			}
			$pageQuery->addHaving(implode(' OR ', $haystackFilters));
		}
		
		// Check if any page types should be excluded from the search.
		// The given page type and all its subclasses will be excluded.
		$excludes = array(
			'SearchExcludePageAggregates' => 'PageAggregate',
			'SearchExcludeErrorPages' => 'ErrorPage'
		);
		$this->extend('updateFindPageIDsExcludes', $excludes);
		foreach ($excludes as $option => $superclass) {
			if ($this->$option) {
				$classes = array_values(ClassInfo::subclassesFor($superclass));
				$classes = implode("', '", $classes);
				$pageQuery->addWhere("\"ClassName\" NOT IN ('$classes')");
			}
		}
		
		// Further filter the result by joining the query with many_many
		// tables, this is mostly necessary in order to support tags
		// and categories.
		$manyManyRelations = array(
			'Categories' => array(
				'table' => 'Page_Categories',
				'pageCol' => 'PageID',
				'relationCol' => 'PageCategoryID',
				'IDs' => Convert::raw2sql($categories)
			),
			'Tags' => array(
				'table' => 'Page_Tags',
				'pageCol' => 'PageID',
				'relationCol' => 'PageTagID',
				'IDs' => Convert::raw2sql($tags)
			)
		);
		$manyManyRequiredKeys = array(
			'table',
			'pageCol',
			'relationCol',
			'IDs'
		);
		$this->extend('updateFindPageIDsManyManyRelations', $manyManyRelations);
		foreach ($manyManyRelations as $name => $relation) {
			if (array_keys($relation) != $manyManyRequiredKeys)
				throw new Exception("Invalid relation spec:" . var_export($relation, true));
			if (count($relation['IDs'])) {
				extract($relation);
				$IDs = implode("', '", $IDs);
				$joinOn = <<<INLINE_SQL
("SiteTree"."ID" = "$table"."$pageCol"
	AND "$table"."$relationCol" IN ('$IDs'))
INLINE_SQL;
				$pageQuery->addInnerJoin($table, $joinOn);
				$pageQuery->selectField("\"$table\".\"ID\"", 'ManyManyID');
			}
		}
		
		// Save all page IDs and the sum of the field weights. Regarding
		// the cache, the assumption is that the query execution will 
		// be the bottle neck.
		$cacheID = sha1($pageQuery->sql());
		if (!isset($this->findPageIDsCache[$cacheID])) {
			$labelWeight = $this->config()->weight_per_label;
			$pageIDs = array();
			$pageRows = $pageQuery->execute();
			foreach ($pageRows as $pageRow) {
				// There can be multiple rows for each Page ID, one for each
				// matching PageLabel (i.e., tag or category). When a Page ID
				// is added to the result set, any weight from the text
				// search is also added, thereafter a constant weight for
				// each additional PageLabel is added.
				$ID = $pageRow['ID'];
				unset($pageRow['ID']);
				unset($pageRow['ManyManyID']);
				if (!isset($pageIDs[$ID])) {
					$pageIDs[$ID] = $labelWeight;
					foreach ($pageRow as $name => $weight)
						$pageIDs[$ID] += (double)$weight;
				} else
					$pageIDs[$ID] += $labelWeight;
			}
			$this->findPageIDsCache[$cacheID] = $pageIDs;
			unset($pageIDs);
			unset($pageRows);
		}
		unset($pageQuery);
		
		return $this->findPageIDsCache[$cacheID];
	}
	
	protected $findPagesCache = array();
	
	/**
	 * Find all pages matching the given search params.
	 * 
	 * @param string $needle
	 * @param array $categories
	 * @param array $tags
	 * @param boolean $cache
	 * 
	 * @return DataList
	 */
	public function findPages($needle, array $categories, array $tags, $cache = true) {
		$pageIDs = $this->findPageIDs($needle, $categories, $tags, $cache);
		
		if (empty($this->SearchResultSort))
			$this->SearchResultSort = self::SEARCH_RESULT_SORT_RELEVANCE;
		
		// If the pages are to be sorted by relevance, then it is
		// necessary to sort the page IDs on their weight.
		if ($this->SearchResultSort == self::SEARCH_RESULT_SORT_RELEVANCE)
			asort($pageIDs, SORT_NUMERIC);
		
		$pageIDs = implode(', ', array_keys($pageIDs));
		$cacheID = sha1($pageIDs);
		if (empty($pageIDs)) {
			$this->findPagesCache[$cacheID] = Page::get()->where('"SiteTree"."ID" < 1');
		} else if (!isset($this->findPagesCache[$cacheID])) {
			$pages = Page::get()->where(sprintf('"SiteTree"."ID" IN (%s)', $pageIDs));
			
			// Relevance sort is tricky, right now it is database specific.
			// In a perfect world, it would not be.
			// 
			// - http://stackoverflow.com/a/396771
			// - http://stackoverflow.com/a/867578
			// - http://stackoverflow.com/a/866521 (slooooooow)
			$databaseClass = get_class(DB::getConn());
			$relevanceSort = array();
			$relevanceSort['MySQLDatabase'] = $relevanceSort['PatchworkMySQLDatabase']
				= sprintf('FIELD("SiteTree"."ID", %s) DESC', $pageIDs);
			
			if (!isset($relevanceSort[$databaseClass])) {
				throw new Exception("sorting on relevance is not supported for $databaseClass");
			}
			
			$resultSort = array(
				self::SEARCH_RESULT_SORT_RELEVANCE => $relevanceSort[$databaseClass],
				self::SEARCH_RESULT_SORT_RANDOM => 'RAND()',
				self::SEARCH_RESULT_SORT_TIMESTAMP => '"Created" DESC',
				self::SEARCH_RESULT_SORT_ALPHABETICAL => '"Title" ASC, "MenuTitle" ASC',
				self::SEARCH_RESULT_SORT_SITETREE => '"Sort" ASC'
			);
			$pages = $pages->sort($resultSort[$this->SearchResultSort]);
			$this->extend('findPagesResult', $pages);
			$this->findPagesCache[$cacheID] = $pages;
		}
		
		return clone $this->findPagesCache[$cacheID];
	}
	
	public function AggregateCategories() {
		return PageCategory::categories_from($this->AggregatePages(true, true));
	}
	
	public function AggregateTags() {
		return PageTag::tags_from($this->AggregatePages(true, true));
	}
	
	public function AggregatePages($cache = true, $originalSearchParams = false) {
		return $this->findPages(
			$this->getSearchParam('Needle', $originalSearchParams),
			$this->getSearchParam('Categories', $originalSearchParams),
			$this->getSearchParam('Tags', $originalSearchParams),
			$cache
		);
	}
	
	public function flushCache($persistent = true) {
		parent::flushCache($persistent);
		
		$this->findPageIDsCache = array();
		$this->findPagesCache = array();
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$fields->findOrMakeTab('Root.Search', $this->fieldLabel('Root_Search'));
		
		$fields->addFieldToTab('Root.Search', new TextField('SearchNeedle',
			$this->fieldLabel('SearchNeedle')));
		
		$fields->addFieldToTab('Root.Search', new NumericField('SearchResultLimit',
			$this->fieldLabel('SearchResultLimit')));
		$fields->addFieldToTab('Root.Search', new NumericField('SearchResultPerPage',
			$this->fieldLabel('SearchResultPerPage')));
		
		$fields->addFieldToTab('Root.Search', $this->dbObject('SearchResultSort')
			->scaffoldFormField($this->fieldLabel('SearchResultSort')));
		
		$fields->addFieldToTab('Root.Search', new CheckboxField('SearchExcludePageAggregates',
			$this->fieldLabel('SearchExcludePageAggregates')));
		$fields->addFieldToTab('Root.Search', new CheckboxField('SearchExcludeErrorPages',
			$this->fieldLabel('SearchExcludeErrorPages')));
		
		return $fields;
	}
	
	public function fieldLabels($includerelations = true) {
		$labels = parent::fieldLabels($includerelations);
		
		$labels['Root_Search'] = _t('PageAggregate.Root_Search', 'Search');
		
		$labels['SearchNeedle'] = _t('PageAggregate.SearchNeedle', 'Needle');
		$labels['SearchResultLimit'] = _t('PageAggregate.SearchResultLimit',
			'Search result limit');
		$labels['SearchResultPerPage'] = _t('PageAggregate.SearchResultPerPage',
			'Search result per page');
		$labels['SearchResultSort'] = _t('PageAggregate.SearchResultSort',
			'Search result sort');
		$labels['SearchExcludePageAggregates'] = _t('PageAggregate.SearchExcludePageAggregates',
			'Search exclude page aggregates');
		$labels['SearchExcludeErrorPages'] = _t('PageAggregate.SearchExcludeErrorPages',
			'Search exclude error pages');
		
		if ($includerelations) {
		}
		
		return $labels;
	}
	
}

class PageAggregate_Controller extends Page_Controller {
	
	private static $allowed_actions = array(
		'SearchForm',
		'search',
		'reset'
	);
	
	public function init() {
		parent::init();
	}
	
	public function PaginatedAggregatePages($cache = true, $originalSearchParams = false) {
		$pagination = new PaginatedList($this->data()->AggregatePages($cache,
			$originalSearchParams), $this->request);
		
		$pageLength = $this->data()->SearchResultPerPage;
		$pagination->setPageLength($pageLength? $pageLength: 10);
		
		return $pagination;
	}
	
	public function SearchForm() {
		$fields = new FieldList(
			$needleField = new TextField('Needle', _t(
				'PageAggregate_Controller.SearchFormNeedleField', 'Needle'))
		);
		
		$addLabelsField = function ($fields, $aggregate, $name) {
			$method = "Aggregate{$name}";
			$field = new CheckboxsetField($name,
				_t("PageAggregate_Controller.SearchForm{$name}Field", $name),
				$aggregate->$method()->map('ID', 'Name')
			);
			if (count($field->getSource()))
				$fields->push($field);
		};
		
		$addLabelsField($fields, $this->data(), 'Categories');
		$addLabelsField($fields, $this->data(), 'Tags');
		
		$this->extend('updateSearchFormFields', $fields);
		
		// Fill the form with any user defined values, i.e. a
		// previously posted search form.
		foreach ($this->data()->getSearchParamNames() as $name) {
			if ($this->data()->getSearchParamMetadata($name, 'changed')) {
				if (($field = $fields->dataFieldByName($name)))
					$field->setValue($this->data()->getSearchParam($name));
			}
		}
		
		$actions = new FieldList(
			$searchAction = new FormAction('search', _t(
				'PageAggregate_Controller.SearchFormSearchAction', 'Search')),
			$clearAction = new FormAction('reset', _t(
				'PageAggregate_Controller.SearchFormResetAction', 'Reset'))
		);
		
		$form = new Form($this, 'SearchForm', $fields, $actions);
		$form->disableSecurityToken();
		$form->setFormMethod('GET');
		$this->extend('updateSearchForm', $form);
		
		return $form;
	}
	
	public function search(array $data, Form $form) {
		$params = $this->data()->getSearchParamNames();
		$model = $this->data();
		$request = $this->getRequest();
		array_walk($params, function ($name) use ($model, $request) {
			$model->setSearchParam($name, $request->getVar($name));
		});
		unset($model);
		unset($request);
		
		return $this->render();
	}
	
	public function reset(array $data, Form $form) {
		return $this->redirect($this->data()->Link());
	}
	
}

}
