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

if (class_exists('SiteTree')) {

class PageSummary extends SiteTreeExtension {
	
	private static $db = array(
		'SummaryTitle' => 'Text',
		'SummaryContent' => 'HTMLText'
	);
	
	private static $has_one = array(
		'SummaryThumbnail' => 'Image'
	);
	
	private static $summary_template_fields = array(
		'PageSummaryTitle' => array(
			'SummaryTitle',
			'Title'
		),
		'PageSummaryContent' => array(
			'SummaryContent',
			'Content'
		),
		'PageSummaryThumbnail' => array(
			'SummaryThumbnailID'
		)
	);
	
	public function updateCMSFields(FieldList $fields) {
		$fieldTransformation = new FormTransformation_SpecificFields(array(
			'SummaryTitle' => 'TextField'
		));
		$this->owner->autoScaffoldExtensionFormFields($fields,
			'Root.PageSummary', get_class($this), $this->owner,
			$fieldTransformation);
	}
	
	public function updateFieldLabels(&$labels) {
		$labels['Root_PageSummary'] = _t('PageSummary.Root_PageSummary', 'Summary');
		
		$labels['SummaryTitle'] = _t('PageSummary.SummaryTitle', 'Title');
		$labels['SummaryContent'] = _t('PageSummary.SummaryContent', 'Content');
		$labels['SummaryThumbnail'] = $labels['SummaryThumbnailID']
			= _t('PageSummary.SummaryThumbnail', 'Thumbnail');
	}
	
	public function Summary($showBadge = true, $showLabels = true,
			$titleTag = 'h3', $badgeType = 'Thumbnail') {
		$templateFields = $this->owner->config()->summary_template_fields;
		$templateValues = array(
			'ShowBadge' => (bool)$showBadge,
			'ShowPageLabels' => (bool)$showLabels,
			'TitleTag' => $titleTag,
			'Badge' => null,
			'BadgeType' => preg_match('/^PageSummary_[_a-zA-Z0-9]+Badge$/', $badgeType)?
				"{$badgeType}": "PageSummary_{$badgeType}Badge"
		);
		
		if (!preg_match('/^h[1-6]$/i', $templateValues['TitleTag'])) {
			throw new \InvalidArgumentException("invalid title tag \"$titleTag\""
				. " - only h1..h6 are supported");
		}
		if (!in_array($templateValues['BadgeType'], PageSummary_Badge::get_implementations())) {
			throw new \InvalidArgumentException("invalid badge type \"$badgeType\""
				. " - use one of " . implode(', ', PageSummary_Badge::get_implementations()));
		}
		
		foreach ($templateFields as $key => $fields) {
			foreach ($fields as $name) {
				$field = $this->owner->dbObject($name);
				
				if ($field instanceof StringField) {
					if (strip_tags($field->RAW()) != '')
						$templateValues[$key] = $field;
				} else if ($field instanceof ForeignKey) {
					$relation = preg_replace('/ID$/', '', $name);
					$relation = $this->owner->$relation();
					if ($relation instanceof Image && $relation->exists())
						$templateValues[$key] = $relation;
				} else {
					throw new Exception(sprintf("%s of type %s is not a supported summary field"
							. " - only Image and StringField subclasses are supported",
						$name, get_class($field)));
				}
				
				if (isset($templateValues[$key]))
					break;
			}
		}
		
		$templateValues['Badge'] = new $templateValues['BadgeType'](
			$this->owner,
			$templateValues
		);
		
		return $this->owner->renderWith('PageSummary', $templateValues);
	}
	
}

abstract class PageSummary_Badge extends ViewableData {
	
	private static $width = 64;
	private static $height = 64;
	
	public static function get_implementations() {
		$classes = ClassInfo::subclassesFor('PageSummary_Badge');
		array_shift($classes);
		return $classes;
	}
	
	/**
	 * @var Page
	 */
	protected $page = null;
	
	/**
	 * @var array
	 */
	protected $values = array();
	
	public function __construct(Page $page, array $values) {
		$this->page = $page;
		$this->values = $values;
	}
	
	abstract public function ImgSrc($forTemplate = true);
	
	public function ImgTag($forTemplate = true) {
		return sprintf('<img src="%s" alt="" />', $this->ImgSrc($forTemplate));
	}
	
	public function forTemplate() {
		return $this->ImgTag();
	}
	
}

class PageSummary_ThumbnailBadge extends PageSummary_Badge {
	
	/**
	 * @var Image
	 */
	protected $image = null;
	
	public function __construct(Page $page, array $values) {
		parent::__construct($page, $values);
		if (isset($values['PageSummaryThumbnail'])) {
			if ($values['PageSummaryThumbnail'] instanceof Image)
				$this->image = $values['PageSummaryThumbnail'];
		}
	}
	
	public function ImgSrc($forTemplate = true) {
		return $this->image? $this->image->CroppedImage(
			$this->config()->width,
			$this->config()->height
		)->getURL(): '';
	}
	
}

class PageSummary_DateBadge extends PageSummary_Badge {
	
	private static $image_dir = 'assets/_datebadge/';
	
	private static $day_y = 32;
	private static $day_font_size = 28;
	
	private static $month_y = 48;
	private static $month_font_size = 10;
	
	private static $year_y = 60;
	private static $year_font_size = 10;
	
	private static $font_file = 'patchwork/fonts/ubuntu/Ubuntu-C.ttf';
	
	protected function generateImage($path, $timestamp) {
		$w = $this->config()->width;
		$h = $this->config()->height;
		
		$zdate = new Zend_Date($timestamp, null, i18n::get_locale());
		
		$dateDay = $zdate->toString(Zend_Date::DAY);
		$dateMonth = mb_strtoupper($zdate->toString(Zend_Date::MONTH_NAME));
		$dateYear = $zdate->toString(Zend_Date::YEAR);
		
		$img = imagecreatetruecolor($w, $h);
		
		$fontPath = Controller::join_links(BASE_PATH, "/{$this->config()->font_file}");
		
		if (!is_file($fontPath))
			throw new \Exception("invalid font \"$fontPath\"");
		
		$textColor = imagecolorallocate($img, 0, 0, 0);
		$bgColor = imagecolorallocate($img, 255, 255, 255);
		
		imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bgColor);
		
		$drawDatePart = function ($fontSize, $yPos, $datePart)
				use ($w, $h, $img, $fontPath, $textColor) {
			$textbox = imageftbbox($fontSize, 0, $fontPath, $datePart);
			$x = ($w - ($textbox[2] - $textbox[0])) / 2;
			$y = $yPos;
			imagefttext($img, $fontSize, 0, $x, $y, $textColor, $fontPath, $datePart);
		};
		
		$drawDatePart($this->config()->day_font_size, $this->config()->day_y, $dateDay);
		$drawDatePart($this->config()->month_font_size, $this->config()->month_y, $dateMonth);
		$drawDatePart($this->config()->year_font_size, $this->config()->year_y, $dateYear);
		
		imagepng($img, $path);
		imagedestroy($img);
	}
	
	public function ImgSrc($forTemplate = true) {
		$imgDir = $this->config()->image_dir;
		
		$timestamp = strtotime($this->page->PublicTimestamp);
		$name = sprintf('%s-%s.png', i18n::get_locale(), date('Y-m-d', $timestamp));
		
		$url = Controller::join_links(BASE_URL . "/$imgDir", "/$name");
		$path = Controller::join_links(BASE_PATH . "/$imgDir", "/$name");
		
		if (!file_exists(dirname($path)))
			Filesystem::makeFolder(dirname($path));
		
		if (!file_exists($path) || isset($_GET['flush']))
			$this->generateImage($path, $timestamp);
		
		return $url;
	}
	
}

class PageSummary_GravatarBadge extends PageSummary_Badge {
	
	private static $imageset = 'identicon';
	
	public function ImgSrc($forTemplate = true) {
		$versions = $this->page->Versions();
		$version = $versions? $versions->Last(): null;
		$author = $version? $version->Author(): null;
		$email = trim($author? $author->Email: 'patchwork@example.com');
		$gravatar = md5(mb_strtolower($email));
		
		return "//www.gravatar.com/avatar/$gravatar?" . http_build_query(array(
			's' => $this->config()->width,
			'd' => $this->config()->imageset,
			'r' => 'g'
		), '', $forTemplate? '&amp;': '&');
	}
	
}

}
