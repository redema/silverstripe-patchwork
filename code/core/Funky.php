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
 * Helper for working with functions and methods.
 */
class Funky {
	
	private function __construct() {
	}
	
	public static function require_args(array $args, $spec) {
		if (is_array($spec)) {
			$spec = implode(', ', $spec);
		} else if (!is_string($spec)) {
			throw new \InvalidArgumentException('$spec must be an array or a string');
		}
		
		// This works perfectly but generates horrible error
		// messages. It is an example where an eval() might
		// actually be the lesser evil. But the verdict is still
		// out on that.
		$lambda = create_function($spec, 'return true;');
		call_user_func_array($lambda, $args);
		return $spec;
	}
	
	public static function exractable_args(array $args, $spec) {
		$spec = self::require_args($args, $spec);
		$tokens = token_get_all("<?php dummy($spec);");
		$names = array();
		$values = array();
		
		foreach ($tokens as $token) {
			if (is_array($token) && $token[0] === T_VARIABLE)
				$names[] = ltrim($token[1], '$');
		}
		
		for ($i = 0; $i < min(count($names), count($args)); $i++) {
			$values[] = $args[$i];
		}
		
		return array_combine($names, $values);
	}
	
}

class FunkyException extends LogicException {
}
