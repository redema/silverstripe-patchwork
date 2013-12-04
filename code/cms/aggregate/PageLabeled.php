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

class PageLabeled extends SiteTreeExtension {
	
	private static $db = array(
		'MetaLabels' => 'Text'
	);
	
	public static function get_implementations() {
		$classes = ClassInfo::subclassesFor('PageLabel');
		array_shift($classes);
		return $classes;
	}
	
	public function updateCMSFields(FieldList $fields) {
		// Page->MetaLabels is automatically updated and should not
		// be edited manually. Therefore it is not added as a CMS
		// field.
	}
	
	public function updateMetaLabels() {
		$metaLabels = implode(', ', array_merge(
			$this->owner->Categories()->map('ID', 'Title')->values(),
			$this->owner->Tags()->map('ID', 'Title')->values()
		));
		
		if ($this->owner->MetaLabels != $metaLabels) {
			// Bypass Versioned since many_many relation updates can not
			// be staged (at this time).
			// 
			// TODO: Use SQLQuery once it supports UPDATE and INSERT as
			// this will probably happen before Versioned supports
			// many_many relations.
			foreach (array('Page', 'Page_Live') as $table) {
				$safeMetaLabels = Convert::raw2sql($metaLabels);
				$updateMetaLabels = <<<INLINE_SQL
UPDATE "$table"
SET "MetaLabels" = '$safeMetaLabels'
WHERE "ID" = {$this->owner->ID};
INLINE_SQL;
				DB::query($updateMetaLabels);
			}
		}
	}
	
	public function onAfterWrite() {
		$this->updateMetaLabels();
	}
	
}

}
