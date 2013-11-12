<?php

define('MYSITE_DIR', 'mysite');
define('MYSITE_PATH', BASE_PATH . '/' . MYSITE_DIR);
define('MYSITE_THIRDPARTY_DIR', MYSITE_DIR . '/' . 'thirdparty');
define('MYSITE_THIRDPARTY_PATH', BASE_PATH . '/' . MYSITE_THIRDPARTY_DIR);

define('PATCHWORK_DIR', 'patchwork');
define('PATCHWORK_PATH', BASE_PATH . '/' . PATCHWORK_DIR);
define('PATCHWORK_THIRDPARTY_DIR', PATCHWORK_DIR . '/' . 'thirdparty');
define('PATCHWORK_THIRDPARTY_PATH', BASE_PATH . '/' . PATCHWORK_THIRDPARTY_DIR);

set_include_path(str_replace('.' . PATH_SEPARATOR, '.' . PATH_SEPARATOR
    . MYSITE_THIRDPARTY_PATH . PATH_SEPARATOR
    . PATCHWORK_THIRDPARTY_PATH . PATH_SEPARATOR,
    get_include_path()));

if (!defined('GROUP_CONTENT_AUTHORS'))
	define('GROUP_CONTENT_AUTHORS', 'content-authors');

if (!defined('GROUP_ADMINISTRATORS'))
	define('GROUP_ADMINISTRATORS', 'administrators');

call_user_func(function () {
	if (Director::isLive()) {
		Director::forceWWW();
	}
	if (PATCHWORK_SSL) {
		Director::forceSSL();
	}
	Session::set_cookie_secure(PATCHWORK_COOKIE_SECURE);
	Session::set_cookie_path(PATCHWORK_COOKIE_PATH);
	
	Member::lock_out_after_incorrect_logins(PATCHWORK_LOGIN_LIMIT);
	
	$database = SS_DATABASE_CLASS;
	if ($database == 'PatchworkMySQLDatabase') {
		$database::set_connection_charset('utf8');
		DataObject::add_extension('Constraint');
	} else {
		trigger_error('constraints not available', E_USER_WARNING);
	}
	
	DataObject::add_extension('DataObjectHelpers');
	DataObject::add_extension('EnforceFieldValues');
	Controller::add_extension('ControllerTemplateHelpers');
	Controller::add_extension('PatchworkRequirements');
	LeftAndMain::add_extension('ResponsiveLeftAndMain');
	
	if (class_exists('SiteTree')) {
		SiteTree::enable_nested_urls();
		SiteTree::add_extension('Autoversioned');
		Page::add_extension('PageSummary');
		Page::add_extension('PageCategorized');
		Page::add_extension('PageTagged');
		ContentController::add_extension('ContentControllerTemplateHelpers');
		
		HtmlEditorConfig::get('cms')->enablePlugins('template');
		HtmlEditorConfig::get('cms')->addButtonsToLine(2, 'template');
		HtmlEditorConfig::get('cms')->setOption('template_external_list_url',
			'/admin/htmleditortemplate/tinymce');
	}
	
	i18n::set_locale(PATCHWORK_I18N_LOCALE);
	i18n::set_default_locale(PATCHWORK_I18N_LOCALE);
	
	Requirements::set_backend(new PatchworkRequirements_Backend());
	
	// Make it easy to run tests with different locales.
	if (in_array(SS_ENVIRONMENT_TYPE, array('dev', 'test')) && preg_match(
			'#^/?/dev/tests/#i', $_SERVER['REQUEST_URI'])) {
		if (isset($_GET['test-i18n']) && i18n::validate_locale($_GET['test-i18n'])) {
			$locale = str_replace('-', '_', $_GET['test-i18n']);
			i18n::set_locale($locale);
			i18n::set_default_locale($locale);
		}
	}
});

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = PATCHWORK_DOMAIN;
	$_SERVER['REQUEST_PORT'] = '80';
}

