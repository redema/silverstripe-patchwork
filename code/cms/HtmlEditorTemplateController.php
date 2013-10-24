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
 * Route: /admin/htmleditortemplate
 */
class HtmlEditorTemplateController extends Controller {
	
	private static $template_dirs = array(
		'patchwork/templates/HtmlEditor/',
		'mysite/templates/HtmlEditor/'
	);
	
	private static $allowed_actions = array(
		'tinymce' => 'ADMIN'
	);
	
	protected function findTemplates() {
		$templateFiles = array();
		$templateDirs = $this->config()->get('template_dirs');
		$dotDirs = array('.', '..');
		foreach ($templateDirs as $templateDir) {
			echo "// src: $templateDir\n";
			if (($absDir = @dir(BASE_PATH . "/$templateDir"))) {
				while (false !== ($entry = $absDir->read())) {
					if (in_array($entry, $dotDirs))
						continue;
					$templateFile = BASE_PATH . "/$templateDir/$entry";
					$templateFileExt = pathinfo($templateFile, PATHINFO_EXTENSION);
					if (is_dir($templateFile) || $templateFileExt !== 'html')
						continue;
					$templateUrl = Controller::join_links(
						BASE_URL,
						"/$templateDir",
						"/$entry"
					);
					$templateName = strtr(basename($templateUrl, '.html'), ".;:", "___");
					$templateFiles[] = array(
						'link' => $templateUrl,
						'name' => _t('HtmlEditorTemplateController.'
							. "{$templateName}_Name", $templateName),
						'desc' => _t('HtmlEditorTemplateController.'
							. "{$templateName}_Desc", $templateName)
					);
				}
				$absDir->close();
			}
		}
		return $templateFiles;
	}
	
	public function index() {
		return $this->httpError(501);
	}
	
	/**
	 * http://www.tinymce.com/wiki.php/plugin:template
	 */
	public function tinymce() {
		$this->response->addHeader('Content-Type', 'text/javascript');
		$templates = array();
		foreach ($this->findTemplates() as $template) {
			list(
				$link,
				$name,
				$desc
			) = array_values($template);
			$templates[] = "\t['$name', '$link', '$desc']";
		}
		return sprintf("var tinyMCETemplateList = [\n%s\n];",
			implode(",\n", $templates));
	}
}
