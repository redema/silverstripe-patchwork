<?php

/**
 * Copyright (c) 2012, Redema AB - http://redema.se/
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

class PageAggregateTest extends FunctionalTest {
	
	public static $fixture_file = 'PageAggregateTest.yml';
	
	protected function resetAggregate(PageAggregate $aggregate) {
		$aggregate->resetSearchParams();
		$aggregate->flushCache();
	}
	
	protected function getLinksFrom(PageAggregate $aggregate) {
		$foundLinks = array();
		foreach ($aggregate->AggregatePages() as $page)
			$foundLinks[] = $page->Link();
		return $foundLinks;
	}
	
	protected function checkLabelSearch(PageAggregate $aggregate, $name,
			PageLabel $label, array $expectedLinks) {
		$this->resetAggregate($aggregate);
		
		$aggregate->$name()->add($label);
		
		$foundLinks = $this->getLinksFrom($aggregate);
		$this->assertEquals($expectedLinks, $foundLinks);
		
		$aggregate->$name()->remove($label);
	}
	
	protected function checkNeedleSearch(PageAggregate $aggregate, $needle,
			array $expectedLinks) {
		$this->resetAggregate($aggregate);
		
		$aggregate->SearchNeedle = $needle;
		
		$foundLinks = $this->getLinksFrom($aggregate);
		$this->assertEquals($expectedLinks, $foundLinks);
		
		$aggregate->SearchNeedle = '';
	}
	
	protected function checkDateSearch(PageAggregate $aggregate,
			$fromDate, $toDate, array $expectedLinks) {
		$this->resetAggregate($aggregate);
		
		$aggregate->SearchFromDate = $fromDate;
		$aggregate->SearchToDate = $toDate;
		
		$foundLinks = $this->getLinksFrom($aggregate);
		$this->assertEquals($expectedLinks, $foundLinks);
		
		$aggregate->SearchFromDate = '';
		$aggregate->SearchToDate = '';
	}
	
	public function testSearch() {
		$newsCategory = $this->objFromFixture('PageCategory', 'news');
		$casesCategory = $this->objFromFixture('PageCategory', 'cases');
		$productsCategory = $this->objFromFixture('PageCategory', 'products');
		
		$aggregate = $this->objFromFixture('PageAggregate', 'blog');
		$aggregate->SearchResultSort = PageAggregate::SEARCH_RESULT_SORT_PUBLICTIMESTAMP;
		
		$this->checkLabelSearch(
			$aggregate,
			'Categories',
			$newsCategory,
			array(
				'/blog/2014/were-not-banging-rocks-together-here/',
				'/blog/2013/introducing-the-new-turret/',
				'/blog/2012/do-panic/',
				'/blog/2012/dont-panic/'
			)
		);
		$this->checkLabelSearch(
			$aggregate,
			'Categories',
			$casesCategory,
			array(
				'/blog/2014/were-not-banging-rocks-together-here/',
				'/blog/2012/do-panic/'
			)
		);
		$this->checkLabelSearch(
			$aggregate,
			'Categories',
			$productsCategory,
			array(
				'/blog/2013/introducing-the-new-turret/'
			)
		);
		
		$this->checkNeedleSearch(
			$aggregate,
			'rocks',
			array(
				'/blog/2014/were-not-banging-rocks-together-here/'
			)
		);
		$this->checkNeedleSearch(
			$aggregate,
			'do',
			array(
				'/blog/2012/do-panic/',
				'/blog/2012/dont-panic/'
			)
		);
		
		// Special case: Needle matching against PageLabels.
		$this->checkNeedleSearch(
			$aggregate,
			'cake',
			array(
				'/blog/2014/were-not-banging-rocks-together-here/',
				'/blog/2012/do-panic/'
			)
		);
		
		$this->checkDateSearch(
			$aggregate,
			'2012-01-01',
			'2012-12-31',
			array(
				'/blog/2012/do-panic/',
				'/blog/2012/dont-panic/',
				'/blog/2012/'
			)
		);
		$this->checkDateSearch(
			$aggregate,
			'2014-01-27',
			'2014-01-28',
			array(
				'/blog/2014/were-not-banging-rocks-together-here/'
			)
		);
	}
	
	protected function checkHierarchy(PageAggregate $aggregate, $type, array $expectedLinks) {
		$aggregate->SearchHierarchy = $type;
		$foundLinks = $this->getLinksFrom($aggregate);
		$this->assertEquals($expectedLinks, $foundLinks);
	}
	
	public function testHierarchy() {
		$aggregate = $this->objFromFixture('PageAggregate', 'blog');
		$aggregate->SearchResultSort = PageAggregate::SEARCH_RESULT_SORT_PUBLICTIMESTAMP;
		
		$this->checkHierarchy(
			$aggregate,
			PageAggregate::SEARCH_GRANDCHILDREN,
			array(
				'/blog/2014/were-not-banging-rocks-together-here/',
				'/blog/2013/introducing-the-new-turret/',
				'/blog/2012/do-panic/',
				'/blog/2012/dont-panic/'
			)
		);
		$this->checkHierarchy(
			$aggregate,
			PageAggregate::SEARCH_CHILDREN,
			array(
				'/blog/2014/',
				'/blog/2013/',
				'/blog/2012/'
			)
		);
		$this->checkHierarchy(
			$aggregate,
			PageAggregate::SEARCH_ALLDESCENDANTS,
			array(
				'/blog/2014/were-not-banging-rocks-together-here/',
				'/blog/2014/',
				'/blog/2013/introducing-the-new-turret/',
				'/blog/2013/',
				'/blog/2012/do-panic/',
				'/blog/2012/dont-panic/',
				'/blog/2012/'
			)
		);
		$this->checkHierarchy(
			$aggregate,
			PageAggregate::SEARCH_SITE,
			array(
				'/blog/2014/were-not-banging-rocks-together-here/',
				'/blog/2014/',
				'/blog/2013/introducing-the-new-turret/',
				'/blog/2013/',
				'/blog/2012/do-panic/',
				'/blog/2012/dont-panic/',
				'/blog/2012/',
				'/'
			)
		);
	}
	
}

