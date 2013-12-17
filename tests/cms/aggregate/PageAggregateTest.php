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
	
	public function testSearch() {
		
	}
	
	protected function checkHierarchy($aggregate, $type, array $expectedLinks) {
		$foundLinks = array();
		$aggregate->SearchHierarchy = $type;
		foreach ($aggregate->AggregatePages() as $page) {
			$foundLinks[] = $page->Link();
		}
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
	}
	
}

