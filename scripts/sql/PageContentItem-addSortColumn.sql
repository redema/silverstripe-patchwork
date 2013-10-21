
-- Add the `Sort` column to PageContentItem.

ALTER TABLE `PageContentItem`
	ADD `Sort` INT NOT NULL
	AFTER `SpecialTemplate`;

ALTER TABLE `PageContentItem_Live`
	ADD `Sort` INT NOT NULL
	AFTER `SpecialTemplate`;

ALTER TABLE `PageContentItem_versions`
	ADD `Sort` INT NOT NULL
	AFTER `SpecialTemplate`;
