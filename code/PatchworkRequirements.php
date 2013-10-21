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
		$requirements = array(
			'mysite-default.css' => array(
				'patchwork/css/thirdparty/bootstrap-3.0.0.css',
				'patchwork/css/thirdparty/bootstrap-theme-3.0.0.css',
				'patchwork/css/FrontEndForm.css',
				'patchwork/css/glue.css',
				'mysite/css/layout.css',
				'mysite/css/typography.css',
				'mysite/css/form.css'
			),
			'mysite-default.js' => array(
				'patchwork/javascript/thirdparty/modernizr-2.6.2.js',
				'patchwork/javascript/thirdparty/respond-ad87635f83f8b811e1da53c082325a4b35960771.js',
				'patchwork/javascript/thirdparty/picturefill-6250f7f6f604c5e016f59ceb121929f87e3ad4d5.js',
				'patchwork/javascript/thirdparty/jquery-1.10.2.js',
				'patchwork/javascript/thirdparty/jquery-migrate-1.1.1.js',
				'patchwork/javascript/thirdparty/jquery-mobile-1.3.1.custom.js',
				'patchwork/javascript/thirdparty/jquery-refineslide-0.4.1.js',
				'patchwork/javascript/thirdparty/bootstrap-3.0.0.js',
				'patchwork/javascript/FrontEndForm.js',
				'patchwork/javascript/Patchwork.js',
				'mysite/javascript/mysite.js'
			),
		);
		
		$cmsUrls = array(
			'admin/',
			'Security/ping'
		);
		$requestUrl = $this->owner->getRequest()->getURL();
		$cmsPageload = false;
		foreach ($cmsUrls as $cmsUrl) {
			$cmsPageload = $cmsPageload|| (
				strlen($requestUrl) >= strlen($cmsUrl) &&
				substr($requestUrl, 0, strlen($cmsUrl)) == $cmsUrl
			);
		}
		
		foreach ($requirements as $combined => $files) {
			if ($cmsPageload)
				Requirements::block($combined);
			else
				Requirements::combine_files($combined, $files);
		}
	}
	
	public function onAfterInit() {
		$reqBackend = Requirements::backend();
	}
	
}

class PatchworkRequirements_Backend extends Requirements_Backend {
	
	public $write_header_comment = false;
	
	protected $trim_combined_css = true;
	
	public function set_trim_combined_css($v) {
		$this->trim_combined_css = (bool)$v;
	}
	
	public function get_trim_combined_css() {
		return $this->trim_combined_css;
	}
	
	protected $trim_combined_js = true;
	
	public function set_trim_combined_js($v) {
		$this->trim_combined_js = (bool)$v;
	}
	
	public function get_trim_combined_js() {
		return $this->trim_combined_js;
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
	
	public static function trimCombinedCSS($css) {
		// This is usually surprisingly adequate, but since it is very
		// simplistic it will probably break something at some point.
		return trim(preg_replace('/\s+/', ' ', $css));
	}
	
	public static function trimCombinedJS($js) {
		// This is mostly micro-optimization, but it saves a few bytes
		// when there are a huge number of combined JS files.
		return trim(preg_replace("/\\n+/", "\n", $js));
	}
	
	/**
	 * Some code duplication is necessary since Requirements_Backend
	 * was never really designed to be a base class. Monkey
	 * patching at its finest.
	 * 
	 * @see Requirements_Backend::process_combined_files()
	 */
	public function process_combined_files() {
		$runningTest = class_exists('SapphireTest', false)?
			SapphireTest::is_running_test(): false;
		if ((Director::isDev() && !$runningTest && !isset($_REQUEST['combine'])) ||
				!$this->combined_files_enabled)
			return;
		
		$combinedFolder = $this->getCombinedFilesFolder();
		$combinedPath = Controller::join_links(BASE_PATH, "/$combinedFolder");
		
		$updatableFiles = array(
			'css' => array(),
			'js' => array()
		);
		
		foreach ($this->get_combine_files() as $combined => $files) {
			$path = Controller::join_links("$combinedPath/", "/$combined");
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$check = (
				($this->trim_combined_css && $ext == 'css') ||
				($this->trim_combined_js && $ext == 'js')
			);
			if ($check) {
				if (!$this->combinedUpToDate($path, $files))
					$updatableFiles[$ext][] = $path;
			}
		}
		
		parent::process_combined_files();
		
		foreach ($updatableFiles as $type => $paths) {
			$trim = 'trimCombined' . strtoupper($type);
			foreach ($paths as $path) {
				$data = file_get_contents($path);
				$data = $this->$trim($data);
				file_put_contents($path, $data);
			}
		}
	}
}
