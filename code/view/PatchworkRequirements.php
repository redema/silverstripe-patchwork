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

class PatchworkRequirements extends Extension {
	
	public function onBeforeInit() {
		$cmsUrls = array(
			'admin/',
			'Security/ping'
		);
		$requestUrl = $this->owner->getRequest()->getURL();
		$cmsPageload = false;
		foreach ($cmsUrls as $cmsUrl) {
			$cmsPageload = $cmsPageload || (
				strlen($requestUrl) >= strlen($cmsUrl) &&
				substr($requestUrl, 0, strlen($cmsUrl)) == $cmsUrl
			);
		}
		
		$requirements = array(
			'mysite-default.css' => array(
				'patchwork/css/thirdparty/bootstrap.css',
				'patchwork/css/thirdparty/bootstrap-theme.css',
				'patchwork/css/thirdparty/colorbox.css',
				'patchwork/fonts/font-awesome/font-awesome.css',
				'patchwork/css/FrontEndForm.css',
				'patchwork/css/PageAggregate.css',
				'patchwork/css/glue.css',
				'mysite/css/layout.css',
				'mysite/css/typography.css',
				'mysite/css/form.css'
			),
			'mysite-default.js' => array(
				'patchwork/javascript/thirdparty/modernizr.js',
				'patchwork/javascript/thirdparty/respond.js',
				'patchwork/javascript/thirdparty/picturefill.js',
				'patchwork/javascript/thirdparty/jquery.js',
				'patchwork/javascript/thirdparty/jquery-migrate.js',
				'patchwork/javascript/thirdparty/jquery-mobile.custom.js',
				'patchwork/javascript/thirdparty/jquery-refineslide.js',
				'patchwork/javascript/thirdparty/jquery-colorbox.js',
				'patchwork/javascript/thirdparty/bootstrap.js',
				'patchwork/javascript/FrontEndForm.js',
				'patchwork/javascript/FrontEndQuirks.js',
				'patchwork/javascript/PageAggregate.js',
				'patchwork/javascript/Patchwork.js',
				'mysite/javascript/mysite.js'
			),
		);
		foreach ($requirements as $combined => $files) {
			if ($cmsPageload)
				Requirements::block($combined);
			else
				Requirements::combine_files($combined, $files);
		}
		
		// Only use the jQuery version that is bundled with patchwork.
		// This might break some SilverStripe functionality, so maybe
		// it should be possible to disable it?
		$blocked = array(
			FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js'
		);
		foreach ($blocked as $file) {
			if (!$cmsPageload)
				Requirements::block($file);
		}
	}
	
	public function onAfterInit() {
		$reqBackend = Requirements::backend();
	}
	
}

class PatchworkRequirements_Backend extends Requirements_Backend {
	
	public $write_header_comment = false;
	
	public $combine_css_with_cssmin = true;
	
	protected $bundledModules = array(
	);
	
	public function addBundledModule($name) {
		$this->bundledModules[$name] = "patchwork/bundle/$name";
	}
	
	public function removeBundledModule($name) {
		unset($this->bundledModules[$name]);
	}
	
	public function getBundledModules() {
		return $this->bundledModules;
	}
	
	/**
	 * @see Requirements::process_combined_files()
	 */
	protected function combinedUpToDate($file, array $components) {
		if (isset($_GET['flush']) && !file_exists($file))
			return false;
		
		$mtime = 0;
		foreach ($components as $component) {
			$path = Controller::join_links(BASE_PATH, "/$component");
			if (file_exists($path))
				$mtime = max(filemtime($path), $mtime);
		}
		return $mtime < filemtime($file);
	}
	
	public function minifyFile($filename, $content) {
		$content = parent::minifyFile($filename, $content);
		$isCSS = pathinfo($filename, PATHINFO_EXTENSION) == 'css';
		if ($isCSS && $this->combine_css_with_cssmin) {
			require_once PATCHWORK_THIRDPARTY_PATH . '/cssmin/cssmin.php';
			$cssMin = new CSSmin($raise_php_limits = false);
			$content = $cssMin->run($content, false) . "\n";
		}
		return $content;
	}
	
	public function includeInHTML($templateFile, $content) {
		$content = parent::includeInHTML($templateFile, $content);
		if (strpos($content, '</head>') !== false) {
			$headtpl = "<!--[if lt IE 9]><script src=\"%s\"></script><![endif]-->\n</head>";
			$html5shiv = Controller::join_links(
				BASE_URL,
				PATCHWORK_DIR,
				'javascript/thirdparty/html5shiv-printshiv-3.7.0.js'
			);
			$head = sprintf($headtpl, $html5shiv);
			$content = str_replace('</head>', $head, $content);
		}
		return $content;
	}
	
	protected function updateBundledPath($file) {
		if ($this->bundledModules) {
			foreach ($this->bundledModules as $name => $path) {
				if (strpos($file, $name) === 0) {
					return Controller::join_links($path, $file);
				}
			}
		}
		return $file;
	}
	
	public function javascript($file) {
		parent::javascript($this->updateBundledPath($file));
	}
	
	public function css($file, $media = null) {
		parent::css($this->updateBundledPath($file), $media);
	}
	
	public function themedCSS($name, $module = null, $media = null) {
		parent::themedCSS($name, $module, $media);
		
		// The default module css could be bundled. It is an
		// unlikely scenario, but handle it anyway.
		if ($this->bundledModules && $module) {
			$css = $this->get_css();
			$default = "$module/css/$name.css";
			$bundled = $this->updateBundledPath($default);
			if (isset($css[$default]) && $default != $bundled) {
				$this->clear($default);
				$this->css($bundled, $media);
			}
		}
	}

}
