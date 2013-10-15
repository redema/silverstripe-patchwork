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
 * <code>
 * class Page extends SiteTree {
 *     private static $has_many = array(
 *         'CarouselItems' => 'PageCarouselItem'
 *     );
 *     public function getCMSFields() {
 *         $fields = parent::getCMSFields();
 *         PageCarouselItem::addCMSFieldsTo($this, $fields,
 *             $this->CarouselItems());
 *         return $fields;
 *     }
 * }
 * class PageCarouselItem extends PageContentItem {
 * }
 * </code>
 */
class PageContentItem extends DataObject {
	private static $db = array(
		'Title' => 'Text',
		'Link' => 'Text',
		'Content' => 'HTMLText',
		'ExtraClasses' => 'Text',
		'SpecialTemplate' => 'Text'
	);
	
	private static $has_one = array(
		'Page' => 'Page',
		'DesktopImage' => 'Image',
		'TabletImage' => 'Image',
		'MobileImage' => 'Image'
	);
	
	private static $constraints = array(
		'Page' => 'on delete cascade'
	);
	
	private static $autoversioned = array(
		'Page' => true
	);
	
	private static $extensions = array(
		"Versioned('Stage', 'Live')",
		"Autoversioned",
		"VersionedHooks",
		"VersionedStatus"
	);
	
	public function Inner($contentClass = '') {
		$templates = array(
			$this->SpecialTemplate,
			$this->ClassName,
			'PageContentItem'
		);
		return $this->renderWith($templates, array(
			'ContentClass' => $contentClass
		));
	}
	
	public function Thumbnail() {
		// Go from smallest to largest and use the first available
		// image for the thumbnail.
		$images = array(
			'MobileImage',
			'TabletImage',
			'DesktopImage'
		);
		foreach ($images as $name) {
			$image = $this->$name();
			if ($image->exists())
				return $image->CroppedImage(32, 32);
		}
		
		// Fallback in case there are no linked images.
		$image = Image::create();
		$image->Name = 'image-missing.png';
		$image->Filename = "patchwork/images/{$image->Name}";
		return $image;
	}
	
	/**
	 * @FIXME: It would be great to have a way to unpublish items
	 * (without deleting the appropriate table rows manually).
	 */
	public static function addCMSFieldsTo(Page $page, FieldList $fields,
			DataList $items) {
		$itemClass = get_called_class();
		$itemClasses = "{$itemClass}s";
		
		$itemsFieldConfig = GridFieldConfig_RelationEditor::create();
		$itemsFieldColumns = $itemsFieldConfig->getComponentByType('GridFieldDataColumns');
		
		$itemsFieldColumns->setDisplayFields(array(
			'Thumbnail' => 'Thumbnail',
			'Title' => 'Title',
			'Link' => 'Link',
			'ExtraClasses' => 'ExtraClasses',
			'PublishedToLive' => 'PublishedToLive'
		));
		
		$itemsField = new GridField(
			$itemClasses,
			$itemClass,
			$items,
			$itemsFieldConfig
		);
		
		$tabName = _t("{$itemClass}.TABNAME", $itemClasses);
		$fields->findOrMakeTab("Root.{$itemClasses}", $tabName);
		$fields->addFieldToTab("Root.{$itemClasses}", $itemsField);
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$tabs = array(
			'Advanced' => array(
				'ExtraClasses' => 'TextField',
				'SpecialTemplate' => 'TextField'
			),
			'Content' => array(
				'Content' => 'HTMLEditorField'
			)
		);
		
		foreach ($tabs as $tab => $specs) {
			$fields->findOrMakeTab("Root.{$tab}", $this->fieldLabel($tab));
			foreach ($specs as $name => $type) {
				$fields->removeByName("Root.{$name}");
				$fields->addFieldToTab("Root.{$tab}", new $type($name,
					$this->fieldLabel($name)));
			}
		}
		
		foreach ($this->db() as $name => $type) {
			if ($type == 'Text' && !($fields->fieldByName($name)
					instanceof TextField))
				$fields->replaceField($name, new TextField($name,
					$this->fieldLabel($name)));
		}
		
		return $fields;
	}
	
}

