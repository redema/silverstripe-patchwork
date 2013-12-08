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

class PatchworkRSSFeed extends RSSFeed {
	
	/**
	 * @see RSSFeed::Entries()
	 */
	public function Entries() {
		$entries = new ArrayList();
		if (isset($this->entries)) {
			foreach ($this->entries as $entry) {
				$selectField = function ($entry, $options) {
					if (is_string($options) || is_null($options))
						return $options;
					else if (is_object($options))
						$options = (array)$options;
					else if (!is_array($options))
						throw new \InvalidArgumentException('invalid $options');
					
					foreach ($options as $field => $sanitizer) {
						if ($sanitizer($entry->$field)) {
							return $field;
						}
					}
					return $field;
				};
				$entries->push(new RSSFeed_Entry(
					$entry,
					$selectField($entry, $this->titleField),
					$selectField($entry, $this->descriptionField),
					$selectField($entry, $this->authorField)
				));
			}
		}
		return $entries;
	}
	
}

