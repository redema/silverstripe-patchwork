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

abstract class CaptchaWord extends Captcha {
	
	private static $length = 8;
	
	private static $glyphs = 'abcdefghijklmnopqrstuvwxyz0123456789';
	
	private static $generator = 'getRandomWord';
	
	private static $dictionary = '/usr/share/dict/words';
	
	public function getFontPath($font) {
		$valid = (
			!empty($font) &&
			mb_strlen($font) > 2
		);
		if ($valid && $font[0] != '/')
			$font = Controller::join_links(BASE_PATH, "/$font");
		
		if (!$valid || !file_exists($font))
			throw new Exception('given font file is not usable');
		
		return $font;
	}
	
	protected function getRandomWord() {
		$length = $this->config()->length;
		$glyphs = $this->config()->glyphs;
		$word = '';
		for ($i = 0; $i < $length; ++$i)
			$word .= $glyphs[mt_rand(0, strlen($glyphs) - 1)];
		return $word;
	}
	
	protected function getDictionaryWord() {
		$words = file($this->config()->dictionary);
		return mb_strtolower($words[mt_rand(0, count($words) - 1)]);
	}
	
	public function getWord($new = false) {
		$key = $this->getSessionKey('Word');
		$word = Session::get($key);
		if (!$word || $new) {
			$generator = $this->config()->generator;
			$word = $this->$generator();
			Session::set($key, $word);
		}
		return $word;
	}
	
	public function reset() {
		$this->getId(true);
		$this->getWord(true);
	}
	
	public function validate($value) {
		return $this->getWord() === $value;
	}
	
}

