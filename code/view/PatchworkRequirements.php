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
				'patchwork/css/thirdparty/bootstrap-3.0.2.css',
				'patchwork/css/thirdparty/bootstrap-theme-3.0.2.css',
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
				'patchwork/javascript/thirdparty/bootstrap-3.0.2.js',
				'patchwork/javascript/FrontEndForm.js',
				'patchwork/javascript/FrontEndQuirks.js',
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
	
}
