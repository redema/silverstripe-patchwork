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

class CaptchaField extends TextField {
	
	protected $backend = null;
	
	public function __construct($name, $title = null, $value = '',
			$backend = 'CaptchaImage', $maxLength = 8, $timeout = 300,
			$form = null) {
		parent::__construct($name, $title, $value, $maxLength, $form);
		
		if (!in_array($backend, $backends = Captcha::get_implementations())) {
			throw new Exception("invalid captcha backend $backend"
				. " - supported backends are: "
				. implode(', ', $backends));
		}
		
		$this->backend = new $backend();
	}
	
	public function validate($validator) {
		if (!$this->backend->validate($this->value)) {
			$validator->validationError($this->name, _t('CaptchaField.ValidationError',
				'Please try again'), 'validation');
			return false;
		}
		$this->backend->reset();
		
		return true;
	}
	
	public function setValue($value, $data = array()) {
		parent::setValue($value, $data);
	}
	
	public function FieldHolder($properties = array()) {
		$fieldHolder = parent::FieldHolder($properties);
		return <<<INLINE_HTML
<div class="captcha">
	<div class="captcha-challenge">{$this->backend->render('img-thumbnail')}</div>
	<div class="captcha-field">{$fieldHolder}</div>
</div>
INLINE_HTML;
	}
	
}
