
ALTER TABLE `PageAggregate`
	DROP `SearchResultLimit`;
ALTER TABLE `PageAggregate_Live`
	DROP `SearchResultLimit`;
ALTER TABLE `PageAggregate_versions`
	DROP `SearchResultLimit`;

ALTER TABLE `PageAggregate`
	CHANGE `SearchResultPerPage` `SearchResultPageLength`
		INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `PageAggregate_Live`
	CHANGE `SearchResultPerPage` `SearchResultPageLength`
		INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `PageAggregate_versions`
	CHANGE `SearchResultPerPage` `SearchResultPageLength`
		INT( 11 ) NOT NULL DEFAULT '0';
