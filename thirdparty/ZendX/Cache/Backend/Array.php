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

require_once 'Zend/Cache/Backend/Interface.php';
require_once 'Zend/Cache/Backend.php';

/**
 * A simplistic non-persistant cache backend for Zend_Cache
 * for Zend Framework 1.*.
 * 
 * @package	ZendX_Cache
 * @subpackage ZendX_Cache_Backend
 */
class ZendX_Cache_Backend_Array extends Zend_Cache_Backend
		implements Zend_Cache_Backend_Interface {
	
	/**
	 * Data array. Format:
	 * <code>
	 * array(
	 *	   $key => array(
	 *	       0 => $value,
	 *	       1 => $timestamp,
	 *	       2 => $lifetime,
	 *	       3 => $tags
	 *	   )
	 * )
	 * </code>
	 */
	protected $data = array();
	
	public function load($id, $doNotTestCacheValidity = false) {
		$timestamp = $this->test($id);
		if ($timestamp || ($doNotTestCacheValidity && isset($this->data[$id]))) {
			list($value) = $this->data[$id];
			return $value;
		}
		return false;
	}
	
	public function test($id) {
		if (isset($this->data[$id])) {
			list($value, $timestamp, $lifetime) = $this->data[$id];
			return time() < (($timestamp + $lifetime))?
				$timestamp: false;
		}
		return false;
	}
	
	public function save($data, $id, $tags = array(), $specificLifetime = false) {
		$now = time();
		$lifetime = $this->getLifetime($specificLifetime);
		$this->data[$id] = array($data, $now, $lifetime, $tags);
		return true;
	}
	
	public function remove($id) {
		if (isset($this->data[$id])) {
			unset($this->data[$id]);
			return true;
		}
		return false;
	}
	
	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tagNeedles = array()) {
		switch ($mode) {
			case Zend_Cache::CLEANING_MODE_ALL:
				$this->data = array();
			break;
			
			case Zend_Cache::CLEANING_MODE_OLD:
				foreach ($this->data as $key => $cached) {
					if (!$this->test($key))
						$this->remove($key);
				}
			break;
			
			default:
				$tagNeedles = (array)$tagNeedles;
				foreach ($this->data as $key => $cached) {
					list($value, $timestamp, $lifetime, $tagHaystack) = $cached;
					$found = count(array_intersect($tagHaystack, $tagNeedles));
					if (($mode == Zend_Cache::CLEANING_MODE_MATCHING_TAG
							&& $found == count($tagNeedles)) ||
						($mode == Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG
							&& $found == 0) ||
						($mode == Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG
							&& $found > 0)) {
						$this->remove($key);
					}
				}
		}
		return true;
	}
	
}

