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
 * TODO: Write documentation.
 */
class Autoversioned extends DataExtension {
	
	public function autoRelations() {
		$autoRelations = array();
		$parentManyComponents = $this->owner->has_many();
		
		foreach ($parentManyComponents as $parentManyName => $childClass) {
			$childObject = Injector::inst()->create($childClass);
			$childComponents = $childObject->has_one();
			foreach ($childComponents as $childComponentName => $parentClass) {
				if ($parentClass == $this->owner->class) {
					$specs = $childObject->config()->get('autoversioned', Config::UNINHERITED);
					if (isset($specs[$childComponentName]) && $specs[$childComponentName]) {
						$autoRelations[$parentManyName] = array(
							$childClass,
							$childComponentName
						);
					}
				}
			}
		}
		return $autoRelations;
	}
	
	/**
	 * Hook into $this->owner->doPublish().
	 */
	public function onAfterPublish() {
		foreach ($this->autoRelations() as $relationName => $relationData) {
			list($class, $field) = $relationData;
			$liveDataObjects = Versioned::get_by_stage($class, 'Live',
				"\"{$class}\".\"{$field}ID\" = {$this->owner->ID}");
			if ($liveDataObjects) {
				foreach ($liveDataObjects as $object) {
					$object->deleteFromStage('Live');
				}
			}
			$stageDataObjects = $this->owner->$relationName();
			if ($stageDataObjects) {
				foreach ($stageDataObjects as $object) {
					if ($object->hasMethod('doPublish')) {
						$object->doPublish();
					} else {
						$canPublishMethod = $object->hasMethod('canPublish');
						if (!$canPublishMethod || ($canPublishMethod &&
								$object->canPublish()))
							$object->publish('Stage', 'Live');
					}
				}
			}
		}
	}
	
	/**
	 * Hook into $this->owner->doUnpublish().
	 */
	public function onAfterUnpublish() {
		foreach ($this->autoRelations() as $relationName => $relationData) {
			list($class, $field) = $relationData;
			$ID = $this->owner->ID? $this->owner->ID: $this->owner->OldID;
			$dataObjects = Versioned::get_by_stage($class, 'Live',
				"\"{$class}\".\"{$field}ID\" = {$ID}");
			if ($dataObjects) {
				foreach ($dataObjects as $object) {
					if ($object->hasMethod('doUnpublish')) {
						$object->doUnpublish();
					} else {
						$canDeleteFromLiveMethod = $object->hasMethod('canDeleteFromLive');
						if (!$canDeleteFromLiveMethod || ($canDeleteFromLiveMethod &&
								$object->canDeleteFromLive()))
							$object->deleteFromStage('Live');
					}
				}
			}
		}
	}
	
}


