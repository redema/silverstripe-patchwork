<?php

/**
 * Zend Framework
 * 
 * LICENSE
 * 
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.12.3
 * 
 * Based on Zend_Captcha_Image - https://github.com/zendframework/zf1/
 * Commit: 395b0873c7c348ea082be0a77c572c8604551056
 * 
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

class CaptchaImage extends CaptchaWord {
	
	private static $image_dir = 'assets/_captcha/';
	
	private static $image_base = '';
	
	private static $image_alt = '';
	
	private static $image_width = 200;
	
	private static $image_height = 50;
	
	private static $font_size = 24;
	
	private static $font_file = 'patchwork/fonts/ubuntu/Ubuntu-R.ttf';
	
	private static $expiration = 300;
	
	private static $dot_noise_level = 100;
	
	private static $line_noise_level = 5;
	
	private static $gc_freq = 10;
	
	private static $regenerate = true;
	
	public function __construct() {
		if (!extension_loaded('gd'))
			throw new Exception('image CAPTCHA requires the GD extension');
		if (!function_exists('imagepng'))
			throw new Exception('image CAPTCHA requires PNG support');
		if (!function_exists('imageftbbox'))
			throw new Exception('image CAPTCHA requires FT font support');
	}
	
	public function getExtension() {
		return 'png';
	}
	
	public function getImageDir() {
		return Controller::join_links(BASE_PATH, "/{$this->config()->image_dir}");
	}
	
	public function getImageUrl() {
		return Controller::join_links(BASE_URL, "/{$this->config()->image_dir}");
	}
	
	public function getFilePath(string $id) {
		return Controller::join_links(
			$this->getImageDir(),
			"/{$id}.{$this->getExtension()}"
		);
	}
	
	public function getFileUrl(string $id) {
		return Controller::join_links(
			$this->getImageUrl(),
			"/{$id}.{$this->getExtension()}"
		);
	}
	
	protected function idUsed($id) {
		return file_exists($this->getFilePath($id));
	}
	
	/**
	 * Remove old files from the image directory.
	 */
	protected function gc() {
		$expire = time() - $this->config()->expiration;
		$imgdir = $this->getImageDir();
		if($imgdir && is_dir($imgdir)) {
			foreach (new DirectoryIterator($imgdir) as $file) {
				if (!$file->isDot() && !$file->isDir()) {
					if (file_exists($file->getPathname()) && $file->getMTime() < $expire) {
						unlink($file->getPathname());
					}
				}
			}
		}
	}
	
	/**
	 * Generate random frequency.
	 *
	 * @return float
	 */
	protected function randomFreq() {
		return mt_rand(700000, 1000000) / 15000000;
	}
	
	/**
	 * Generate random phase.
	 *
	 * @return float
	 */
	protected function randomPhase() {
		// Random phase from 0 to pi.
		return mt_rand(0, 3141592) / 1000000;
	}
	
	/**
	 * Generate random character size.
	 *
	 * @return int
	 */
	protected function randomSize() {
		return mt_rand(300, 700) / 100;
	}
	
	public function generate() {
		$id = $this->getId();
		$regenerate = $this->config()->regenerate;
		
		if (!file_exists($this->getFilePath($id)) || $regenerate)
			$this->generateImage($id, $this->getWord());
		
		if (mt_rand(1, $this->config()->gc_freq) == 1)
			$this->gc();
		
		return $id;
	}
	
	/**
	 * Override this function if you want different image generator.
	 * Wave transform from http://www.captcha.ru/captchas/multiwave/
	 */
	protected function generateImage($id, $word) {
		if (!file_exists($this->getImageDir()))
			Filesystem::makeFolder($this->getImageDir());
		
		$font = $this->getFontPath($this->config()->font_file);

		$w = $this->config()->image_width;
		$h = $this->config()->image_height;
		$fsize = $this->config()->font_size;
		
		$img_file = $this->getFilePath($id);
		$img_base = $this->config()->image_base;
		if (empty($img_base)) {
			$img = imagecreatetruecolor($w, $h);
		} else {
			if (!($img = imagecreatefrompng($img_base)))
				throw new Exception("$img_base could not be loaded");
			$w = imagesx($img);
			$h = imagesy($img);
		}
		$text_color = imagecolorallocate($img, 0, 0, 0);
		$bg_color = imagecolorallocate($img, 255, 255, 255);
		
		imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bg_color);
		
		$textbox = imageftbbox($fsize, 0, $font, $word);
		$x = ($w - ($textbox[2] - $textbox[0])) / 2;
		$y = ($h - ($textbox[7] - $textbox[1])) / 2;
		imagefttext($img, $fsize, 0, $x, $y, $text_color, $font, $word);
		
		// Generate noise.
		for ($i = 0; $i < $this->config()->dot_noise_level; $i++) {
			imagefilledellipse(
				$img,
				mt_rand(0, $w),
				mt_rand(0, $h),
				2,
				2,
				$text_color
			);
		}
		for ($i = 0; $i < $this->config()->line_noise_level; $i++) {
			imageline(
				$img,
				mt_rand(0, $w),
				mt_rand(0, $h),
				mt_rand(0, $w),
				mt_rand(0, $h),
				$text_color
			);
		}
		
		$img2 = imagecreatetruecolor($w, $h);
		$bg_color = imagecolorallocate($img2, 255, 255, 255);
		
		imagefilledrectangle($img2, 0, 0, $w - 1, $h - 1, $bg_color);
		
		// Apply wave transforms.
		$freq1 = $this->randomFreq();
		$freq2 = $this->randomFreq();
		$freq3 = $this->randomFreq();
		$freq4 = $this->randomFreq();

		$ph1 = $this->randomPhase();
		$ph2 = $this->randomPhase();
		$ph3 = $this->randomPhase();
		$ph4 = $this->randomPhase();

		$szx = $this->randomSize();
		$szy = $this->randomSize();
		
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$sx = $x + (sin($x * $freq1 + $ph1) + sin($y * $freq3 + $ph3)) * $szx;
				$sy = $y + (sin($x * $freq2 + $ph2) + sin($y * $freq4 + $ph4)) * $szy;
				
				if ($sx < 0 || $sy < 0 || $sx >= $w - 1 || $sy >= $h - 1) {
					continue;
				} else {
					$color = (imagecolorat($img, $sx, $sy) >> 16) & 0xFF;
					$color_x = (imagecolorat($img, $sx + 1, $sy) >> 16) & 0xFF;
					$color_y = (imagecolorat($img, $sx, $sy + 1) >> 16) & 0xFF;
					$color_xy = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
				}
				if ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
					// Ignore background. FIXME: Backgrounds with other colors.
					continue;
				} else if ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
					// Transfer inside of the image as-is.
					$newcolor = 0;
				} else {
					// Do antialiasing for border items.
					$frac_x = $sx - floor($sx);
					$frac_y = $sy - floor($sy);
					$frac_x1 = 1 - $frac_x;
					$frac_y1 = 1 - $frac_y;
					
					$newcolor = $color * $frac_x1 * $frac_y1
						+ $color_x * $frac_x * $frac_y1
						+ $color_y * $frac_x1 * $frac_y
						+ $color_xy * $frac_x * $frac_y;
				}
				imagesetpixel(
					$img2,
					$x,
					$y,
					imagecolorallocate(
						$img2,
						$newcolor,
						$newcolor,
						$newcolor
					)
				);
			}
		}

		// Generate noise.
		for ($i = 0; $i < $this->config()->dot_noise_level; $i++) {
			imagefilledellipse(
				$img2,
				mt_rand(0, $w),
				mt_rand(0, $h),
				2,
				2,
				$text_color
			);
		}
		for ($i = 0; $i < $this->config()->line_noise_level; $i++) {
			imageline(
				$img2,
				mt_rand(0, $w),
				mt_rand(0, $h),
				mt_rand(0, $w),
				mt_rand(0, $h),
				$text_color
			);
		}
		
		imagepng($img2, $img_file);
		imagedestroy($img);
		imagedestroy($img2);
	}
	
	public function render($class = '') {
		$id = $this->generate();
		$tpl = '<img width="%d" height="%d" alt="%s" src="%s" class="%s" />';
		return sprintf($tpl,
			$this->config()->image_width,
			$this->config()->image_height,
			$this->config()->image_alt,
			$this->getFileUrl($id),
			$class
		);
	}
	
}


