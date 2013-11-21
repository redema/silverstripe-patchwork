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

abstract class Captcha extends Object {
	
	protected function getSessionKey($postfix) {
		return 'patchwork_' . get_class($this) . "_$postfix";
	}
	
	protected function idUsed($id) {
		return false;
	}
	
	public static function get_implementations() {
		$classes = ClassInfo::subclassesFor('Captcha');
		array_shift($classes);
		foreach ($classes as $class) {
			$reflection = new ReflectionClass($class);
			if ($reflection->isAbstract())
				unset($classes[$class]);
		}
		return $classes;
	}
	
	abstract public function reset();
	abstract public function render();
	abstract public function validate($value);
	
	public function getId($new = false) {
		$key = $this->getSessionKey('Id');
		$id = Session::get($key);
		if (!$id || $new) {
			do {
				$id = sha1(uniqid(microtime(true), true));
			} while ($this->idUsed($id));
			Session::set($key, $id);
		}
		return $id;
	}
	
}

