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

class ConstraintTest extends SapphireTest {
	
	public static $fixture_file = 'ConstraintTest.yml';
	
	protected function checkStagePresence($class, $stage, $ID, $assert) {
		$object = Versioned::get_one_by_stage($class, $stage, $ID, false);
		$this->$assert(empty($object) === false);
	}
	
	protected function checkTableRow($table, $column, $value, $assert) {
		$r = DB::query(sprintf("SELECT \"%s\" FROM \"%s\" WHERE \"%s\" = '%s'",
			$column, $table, $column, (string)$value));
		$this->$assert($r->numRecords() > 0);
	}
	
	public function testNormalDelete() {
		$order = new ConstraintTest_Order();
		$orderID = $order->write();
		
		for ($i = 0, $items = array(); $i < 4; $i++) {
			$item = new ConstraintTest_OrderItem();
			$item->Price = $i + 42;
			$item->Quantity = pow(2, $i);
			$item->OrderID = $order->ID;
			$itemID = $item->write();
			$items[$itemID] = $item;
			$item = null;
		}
		
		$order->delete();
		
		$this->checkTableRow($order->class, 'ID', $orderID, 'assertFalse');
		foreach ($items as $itemID => $item) {
			$this->checkTableRow($item->class, 'ID', $itemID, 'assertFalse');
		}
	}
	
	public function testVersionedDelete() {
		$page = new ConstraintTest_WidgetPage();
		$page->Title = 'Widgety';
		$pageID = $page->write();
		$page->doPublish();
		
		for ($i = 0, $widgets = array(); $i < 8; $i++) {
			$widget = new ConstraintTest_Widget();
			$widget->PageID = $page->ID;
			$widgetID = $widget->write();
			$widget->doPublish();
			$widgets[$widgetID] = $widget;
			$widget = null;
		}
		
		$page->doUnpublish();
		$this->checkStagePresence($page->class, 'Live',
			$pageID, 'assertFalse');
		foreach ($widgets as $widgetID => $widget) {
			$this->checkStagePresence($widget->class, 'Live',
				$widgetID, 'assertFalse');
		}
		
		$page->delete();
		$this->checkStagePresence($page->class, 'Stage',
			$pageID, 'assertFalse');
		foreach ($widgets as $widgetID => $widget) {
			$this->checkStagePresence($widget->class, 'Stage',
				$widgetID, 'assertFalse');
		}
	}
	
	public function testRelatedDelete() {
		$page = new Page();
		$page->Title = 'Volatile';
		$pageID = $page->write();
		
		$page = $page->newClassInstance('VirtualPage');
		$page->write();
		$page = VirtualPage::get()->byID($pageID);
		$this->assertInstanceOf('VirtualPage', $page);
		
		$page = $page->newClassInstance('RedirectorPage');
		$page->write();
		$page = RedirectorPage::get()->byID($pageID);
		$this->assertInstanceOf('RedirectorPage', $page);
		
		$page->delete();
		
		$tables = array(
			'SiteTree',
			'VirtualPage',
			'RedirectorPage'
		);
		foreach ($tables as $table) {
			$this->checkTableRow($table, 'ID', $pageID, 'assertFalse');
		}
	}
	
}

/**
 * Test implementation details.
 * @ignore
 * #@+
 */
class ConstraintTest_Order extends DataObject {
	private static $db = array(
		'Status' => "Enum(Array(
			'New',
			'Packed',
			'Delivered'
		), 'New')"
	);
	private static $has_many = array(
		'Items' => 'ConstraintTest_OrderItem'
	);
}
class ConstraintTest_OrderItem extends DataObject {
	private static $db = array(
		'Quantity' => 'Int',
		'Price' => 'Decimal'
	);
	private static $has_one = array(
		'Order' => 'ConstraintTest_Order'
	);
	private static $constraints = array(
		'Order' => 'on delete cascade'
	);
}
class ConstraintTest_WidgetPage extends Page {
	private static $has_many = array(
		'Widgets' => 'ConstraintTest_Widget'
	);
}
class ConstraintTest_Widget extends DataObject {
	private static $has_one = array(
		'Page' => 'ConstraintTest_WidgetPage'
	);
	private static $constraints = array(
		'Page' => 'on delete cascade'
	);
	private static $extensions = array(
		"Versioned('Stage', 'Live')",
		"Autoversioned",
		"VersionedHooks"
	);
}
/**
 * #@-
 */

