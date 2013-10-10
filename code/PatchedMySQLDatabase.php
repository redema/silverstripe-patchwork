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
 * Add support for InnoDB constraints.
 * 
 * @see PatchedForeignKey
 * @see Constraint
 */
class PatchedMySQLDatabase extends MySQLDatabase {
	
	public function supportConstraints() {
		return true;
	}
	
	public function constraintName($srcTable, $srcColumn,
			$refTable, $refColumn) {
		// Work around constraint name length limitations by using
		// md5 sums (the names are not that important, provided that
		// they are always known and reproducible).
		return md5("{$srcTable}_{$srcColumn}__{$refTable}_{$refColumn}");
	}
	
	public function findConstraint($table, $name) {
		$search = <<<INLINE_SQL
select
	`COLUMN_NAME`
from
	`INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
where
	`TABLE_NAME` = '{$table}' AND
	`TABLE_SCHEMA` = '{$this->database}' AND
	`CONSTRAINT_NAME` = '${name}';
INLINE_SQL;
		
		return $this->query($search)->numRecords() > 0;
	}
	
	public function clearTable($table) {
		$this->query('SET FOREIGN_KEY_CHECKS = 0');
		$this->query("TRUNCATE \"$table\"");
		$this->query('SET FOREIGN_KEY_CHECKS = 1');
	}
	
	public function createConstraint($srcTable, $srcColumn,
			$refTable, $refColumn, $action) {
		// Both $srcTable and $refTable must be either Versioned or
		// unVersioned at this point. See transCreateConstraint().
		$tablePostfixes = Constraint::table_postfixes($srcTable);
		//var_dump("$srcTable - $refTable " . var_export($tablePostfixes, true));
		foreach ($tablePostfixes as $tablePostfix) {
			$actualSrcTable = "{$srcTable}{$tablePostfix}";
			$actualRefTable = "{$refTable}{$tablePostfix}";
			
			$name = $this->constraintName($actualSrcTable, $srcColumn,
				$actualRefTable, $refColumn);
			
			// Nothing must be done if the constraint exists.
			if ($this->findConstraint($actualSrcTable, $name))
				continue;
			
			$constraint = "ALTER TABLE \"$actualSrcTable\""
				. " ADD CONSTRAINT \"$name\""
				. " FOREIGN KEY(\"$srcColumn\")"
				. " REFERENCES \"$actualRefTable\"(\"$refColumn\")"
				. " $action";
			
			$this->query($constraint);
			$this->alterationMessage("Table $actualSrcTable:"
					. " created foreign key $srcColumn => $actualRefTable.$refColumn"
					. " ($action)",
				'created');
		}
	}
	
	public function transCreateConstraint($srcTable, $srcColumn,
			$refTable, $refColumn, $action) {
		// Versioned DataObjects needs special treatment to ensure
		// that users do not accidently screw them self over (for
		// example by connecting non-Versioned DOs to Versioned DOs
		// and then deleting from the draft site or unpublishing).
		$srcVersioned = $srcTable::has_extension('Versioned');
		$refVersioned = $refTable::has_extension('Versioned');
		
		// Ungly error handling, but quite informative. It will do
		// for now.
		if ($srcVersioned && !$refVersioned)
			$this->databaseError("{$srcTable} is versioned, but {$refTable} is not");
		if ($refVersioned && !$srcVersioned)
			$this->databaseError("{$refTable} is versioned, but {$srcTable} is not");
		
		$this->transInitTable($srcTable);
		$this->schemaUpdateTransaction[$srcTable]['patchyConstraints'] = array(
			'srcTable' => $srcTable,
			'srcColumn' => $srcColumn,
			'refTable' => $refTable,
			'refColumn' => $refColumn,
			'action' => $action
		);
	}
	
	public function endSchemaUpdate() {
		$schemaUpdateTransactionCopy = $this->schemaUpdateTransaction;
		parent::endSchemaUpdate();
		$this->schemaIsUpdating = true;
		
		foreach ($schemaUpdateTransactionCopy as $table => $changes) {
			if (isset($changes['patchyConstraints']))
				call_user_func_array(array($this, 'createConstraint'),
					array_values($changes['patchyConstraints']));
		}
		
		$this->schemaIsUpdating = false;
	}
	
	public function requireTable($table, $fieldSchema = null, $indexSchema = null,
			$hasAutoIncPK = true, $options = array(), $extensions = false) {
		// Foreign keys must allow NULL values.
		array_walk($fieldSchema, function (&$type, $name) {
			if ($type == 'ForeignKey')
				$type = 'PatchedForeignKey';
		});
		parent::requireTable($table, $fieldSchema, $indexSchema, $hasAutoIncPK,
			$options, $extensions);
	}
	
	/**
	 * MySQLDatabase::int(...) will ignore everything passed to
	 * it that is not a ("correct") default value. Fix that.
	 */
	public function int($values) {
		$precision = isset($values['precision'])? $values['precision']: 11;
		$null = isset($values['null'])? $values['null']: 'not null';
		$default = isset($values['default'])? $values['default']: 0;
		return "int($precision) $null default $default";
	}
	
}

