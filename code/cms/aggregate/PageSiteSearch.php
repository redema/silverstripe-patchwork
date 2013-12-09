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

class PageSiteSearch extends Controller {
	
	private static $allowed_actions = array(
		'SearchForm'
	);
	
	private static $result_page_length = 10;
	
	public static function build_link($needle, $sort, $fromDate, $toDate,
			array $categories, array $tags, $forTemplate) {
		$separator = $forTemplate? '&amp;': '&';
		$params = array(
			'Needle' => $needle,
			'Sort' => $sort,
			'FromDate' => $fromDate,
			'ToDate' => $toDate,
			'Categories' => $categories,
			'Tags' => $tags
		);
		return Controller::join_links(
			'/' . get_called_class(),
			'?' . http_build_query($params, '', $separator)
		);
	}
	
	public function index() {
		$aggregate = PageAggregate::create();
		
		$aggregate->Title = _t('PageSiteSearch.Search', 'Search');
		$aggregate->URLSegment = get_class($this);
		$aggregate->SearchResultSort = PageAggregate::SEARCH_RESULT_SORT_RELEVANCE;
		$aggregate->SearchResultPageLength = $this->config()->result_page_length;
		
		$controller = PageAggregate_Controller::create($aggregate);
		$controller->setDataModel($this->model);
		$controller->setRequest($this->request);
		$controller->init();
		
		$response = $controller->getResponse();
		
		if ($response && $response->isFinished())
			return $response;
		
		return $controller->search(array(), null);
	}
	
	public function SearchForm() {
		if ($this->request->getVar('action_reset')) {
			return $this->redirect($this->Link());
		}
		return $this->redirect(self::build_link(
			$this->request->getVar('Needle'),
			$this->request->getVar('Sort'),
			$this->request->getVar('DateFrom'),
			$this->request->getVar('DateTo'),
			(array)$this->request->getVar('Categories'),
			(array)$this->request->getVar('Tags'),
			false
		));
	}
	
}

}
