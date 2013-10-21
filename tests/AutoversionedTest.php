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

class AutoversionedTest extends SapphireTest {
	
	public static $fixture_file = 'AutoversionedTest.yml';
	
	protected function checkStagedDataObjects($class, $count, $stage) {
		$objects = Versioned::get_by_stage($class, $stage);
		$this->assertInstanceOf('DataList', $objects);
		$this->assertEquals($count, count($objects));
	}
	
	public function testAutoPublishAndUnpublish() {
		$page = $this->objFromFixture('AutoversionedTest_Page', 'page1');
		$widgets = $page->Widgets();
		$this->assertInstanceOf('DataList', $widgets);
		$this->assertEquals(3, count($widgets));
		
		$this->assertTrue($page->doPublish());
		$this->checkStagedDataObjects('AutoversionedTest_Widget', 3, 'Live');
		$this->checkStagedDataObjects('AutoversionedTest_Gadget', 2, 'Live');
		
		$this->assertTrue($page->doUnpublish());
		$this->checkStagedDataObjects('AutoversionedTest_Widget', 0, 'Live');
		$this->checkStagedDataObjects('AutoversionedTest_Gadget', 0, 'Live');
		
		$widgets = $page->Widgets();
		$this->assertInstanceOf('DataList', $widgets);
		$this->assertEquals(3, count($widgets));
	}
	
}

/**
 * Test implementation details.
 * @ignore
 * #@+
 */
class AutoversionedTest_Page extends Page {
	private static $has_many = array(
		'Widgets' => 'AutoversionedTest_Widget'
	);
}
class AutoversionedTest_Widget extends DataObject {
	private static $has_one = array(
		'Page' => 'AutoversionedTest_Page'
	);
	private static $has_many = array(
		'Gadgets' => 'AutoversionedTest_Gadget'
	);
	private static $extensions = array(
		"Versioned('Stage', 'Live')",
		"Autoversioned",
		"VersionedHooks"
	);
	private static $autoversioned = array(
		'Page' => true
	);
}
class AutoversionedTest_SuperWidget extends AutoversionedTest_Widget {
	private static $db = array(
		'Name' => 'Text'
	);
}
class AutoversionedTest_Gadget extends DataObject {
	private static $has_one = array(
		'Widget' => 'AutoversionedTest_Widget'
	);
	private static $extensions = array(
		"Versioned('Stage', 'Live')",
		"Autoversioned",
		"VersionedHooks"
	);
	private static $autoversioned = array(
		'Widget' => true
	);
}
/**
 * #@-
 */

