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

/**
 * <h1>Summary</h1>
 * 
 * Add support for simplistic foreign key constraints.
 * 
 * The support for constraints on Versioned DataObjects is
 * very limited, only objects with identically configured
 * Versioned extensions can have constraints.
 * 
 * It is currently not possible to handle many_many-relations
 * since they do not support Versioned very well (at all).
 * 
 * <code>
 * class Widget extends DataObject {
 *     public static $has_one = array(
 *         'Page' => 'Page'
 *     );
 *     public static $constraints = array(
 *         'Page' => 'on delete cascade'
 *     );
 *     public static $extensions = array(
 *          "Versioned('Stage', 'Live')"
 *     );
 * }
 * </code>
 */
class Constraint extends DataExtension {
	
	public static function table_postfixes($class) {
		return $class::has_extension('Versioned')?
			array('', '_Live'): array('');
	}
	
	/**
	 * It is not enough to know if the given $class has a table
	 * now, it might have one soon (in the case of a bigger db
	 * build).
	 */
	private function tableRequired($class) {
		foreach (array('db', 'has_one') as $property) {
			if (count(Config::inst()->get($class, $property, Config::UNINHERITED)))
				return true;
		}
		return false;
	}
	
	/**
	 * DataObject::write() will not work properly with foreign
	 * key constraints since these must be explicitly set to
	 * NULL values when a row is inserted.
	 */
	public function onBeforeWrite() {
		$baseClass = ClassInfo::baseDataClass($this->owner->class);
		$baseConstraints = Config::inst()->get($this->owner->class,
			'constraints', Config::UNINHERITED);
		
		if ($baseConstraints && empty($this->owner->ID)) {
			$fields = array_keys($baseConstraints);
			array_walk($fields, function (&$field) {
				$field = "\"{$field}ID\"";
			});
			
			$now = DB::getConn()->now();
			$nulls = array_fill(0, count($fields), 'NULL');
			$fields = implode(', ', $fields);
			$nulls = implode(', ', $nulls);
			DB::query("INSERT INTO \"$baseClass\" (\"Created\", $fields)"
				. " VALUES ($now, $nulls)");
			
			$this->owner->ID = DB::getGeneratedID($baseClass);
		}
	}
	
	/**
	 * Augment writes to make sure that constraints with the zero
	 * values are replaced with NULLs.
	 */
	public function augmentWrite(&$manipulation) {
		if ($manipulation) {
			foreach ($manipulation as $table => $write) {
				if (!isset($write['fields']))
					continue;
				$fields = $write['fields'];
				$constraints = Config::inst()->get($table,
					'constraints', Config::UNINHERITED);
				if ($constraints) {
					foreach ($constraints as $field => $action) {
						$field = "{$field}ID";
						if (isset($fields[$field]) && $fields[$field] < 1)
							$manipulation[$table]['fields'][$field] = 'NULL';
					}
				}
			}
		}
	}
	
	public function augmentDatabase() {
		$connection = DB::getConn();
		
		// Sanity check.
		if (!method_exists($connection, 'supportConstraints') ||
				!$connection->supportConstraints()) {
			throw new Exception(sprintf('%s can probably not handle constraints',
				get_class($connection)));
		}
		
		// Deal with parents (using $this->owner->ID).
		if ($this->tableRequired($this->owner)) {
			$baseTable = ClassInfo::baseDataClass($this->owner->class);
			if ($this->owner->class !== $baseTable) {
				$connection->transCreateConstraint($this->owner->class, 'ID',
					$baseTable, 'ID', 'on delete cascade');
			}
		}
		
		// Deal with $has_one-relations.
		$components = Config::inst()->get($this->owner->class,
			'has_one', Config::UNINHERITED);
		$constraints = Config::inst()->get($this->owner->class,
			'constraints', Config::UNINHERITED);
		
		if ($components && $constraints) {
			foreach ($components as $componentName => $componentClass) {
				if (isset($constraints[$componentName])) {
					$srcTable = $this->owner->class;
					$srcColumn = "{$componentName}ID";
					$refTable = ClassInfo::baseDataClass($componentClass);
					$refColumn = 'ID';
					$action = $constraints[$componentName];
					
					$connection->transCreateConstraint($srcTable, $srcColumn,
						$refTable, $refColumn, $action);
				}
			}
		}
	}
	
}

