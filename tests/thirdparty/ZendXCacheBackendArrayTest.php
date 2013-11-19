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

require_once 'Zend/Cache.php';
require_once 'ZendX/Cache/Backend/Array.php';

class ZendXCacheBackendArrayTest extends SapphireTest {
	
	protected function assertKeys(Zend_Cache_Core $cache, array $keys) {
		foreach ($keys as $key)
			$this->assertTrue($cache->test($key) > 0);
	}
	
	public function testBackend() {
		$cache = Zend_Cache::factory(
			'Core',
			'ZendX_Cache_Backend_Array',
			$frontendOptions = array(
				'lifetime' => 2,
				'automatic_serialization' => true
			),
			$backendOptions = array(
			),
			$customFrontendNaming = true,
			$customBackendNaming = true,
			$autoload = false
		);
		
		$this->assertInstanceOf('Zend_Cache_Core', $cache);
		
		$this->assertFalse($cache->load('k0'));
		$cache->save('v0');
		$this->assertTrue($cache->test('k0') > 0);
		$this->assertEquals('v0', $cache->load('k0'));
		
		$cache->save('v1', 'k1', array('t1'));
		$cache->save('v2', 'k2', array('t1', 't2'));
		$cache->save('v3', 'k3', array('t1', 't2', 't3'));
		$cache->save('v4', 'k4', array('t3', 't4'));
		$cache->save('v4', 'k5', array('t2', 't5'));
		
		$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,
			array('t1', 't2', 't3'));
		$this->assertFalse($cache->test('k3'));
		$this->assertKeys($cache, array('k1', 'k2', 'k4', 'k5'));
		
		$cache->clean(Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG,
			array('t1', 't2'));
		$this->assertFalse($cache->test('k4'));
		$this->assertKeys($cache, array('k1', 'k2', 'k5'));
		
		$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
			array('t1', 't2'));
		foreach (array('k1', 'k2', 'k3') as $key)
			$this->assertFalse($cache->test($key));
		
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
		$this->assertFalse($cache->test('k0'));
		
		$cache->save('v0', 'k0');
		sleep(2);
		$cache->clean(Zend_Cache::CLEANING_MODE_OLD);
		$this->assertFalse($cache->test('k0'));
	}
	
}

