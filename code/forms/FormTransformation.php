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
 * Transform fields by name. Very useful in combination with
 * DataObjectHelpers::autoScaffoldExtensionFormFields().
 */
class FormTransformation_SpecificFields extends FormTransformation {
	
	protected $fields = array();
	
	public function __construct(array $fields) {
		$this->fields = $fields;
	}
	
	protected function interceptable($method) {
		return preg_match('/^transform[_a-z0-9]+/i', $method);
	}
	
	protected function intercept($method, $arguments) {
		$field = array_shift($arguments);
		$name = $field->getName();
		if (isset($this->fields[$name])) {
			$transformer = $this->fields[$name];
			$field = is_callable($transformer)?
				$transformer($field): $field->castedCopy($transformer);
		}
		return $field;
	}
	
	public function hasMethod($method) {
		return $this->interceptable($method)?
			true: parent::hasMethod($method);
	}
	
	public function __call($method, $arguments) {
		return $this->interceptable($method)?
			$this->intercept($method, $arguments):
			parent::__call($method, $arguments);
	}
	
	public function addField($name, $transformer) {
		$this->fields[$name] = $transformer;
	}
	
	public function removeField($name) {
		unset($this->fields[$name]);
	}
	
}
