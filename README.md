# SilverStripe patchwork Module

patchwork is part framework, part boilerplate and part mixed utilities,
extensions and third party libraries. Used correctly, it can help you
create awesome SilverStripe sites if you like its convention over
configuration philosophy. It can be used with or without the SilverStripe
CMS module, SilverStripe framework is however required for obvious reasons.

Theoretically speaking it might be better to split patchwork into smaller
and more logically related modules, practically speaking it saves time
to only worry about one module (maintenance, dependencies, and so on).

Disclaimer: This module is a work in progress, anything and everything may
change at any time right now. However, things are getting more and more
stable.

## Maintainer Contact

Redema AB, http://redema.se/

## Requirements

 * PHP: 5.3.1+ minimum.
 * Database: MySQL 5.5+ minimum.
 * SilverStripe framework: 3.1+ minimum (previous versions has never been tested).

## Recommendations

 * PHPUnit - https://github.com/sebastianbergmann/phpunit/
 * Additional SilverStripe modules:
   * CMS - https://github.com/silverstripe/silverstripe-cms
   * SortableGridField - https://github.com/UndefinedOffset/SortableGridField
   * GoogleSiteMaps - https://github.com/silverstripe-labs/silverstripe-googlesitemaps

## Getting started with a new project

 * Place this directory in the root of your SilverStripe installation. Make sure
   that the folder is named "patchwork".

 * Copy the example environment file and edit it, remove everything not
   needed in `mysite/_config.php`:
   * `www$ cp patchwork/_ss_environment.php.example _ss_environment.php`
   * `www$ ed _ss_environment.php`
   * `www$ ed mysite/_config.php`

 * Copy the standard Page template to mysite and create overrides for
   `Header.ss`, `Footer.ss` and `Page.ss`:
   * `www$ cp patchwork/templates/Page.ss mysite/templates/`
   * `www$ echo "" > mysite/templates/Includes/Header.ss`
   * `www$ echo "" > mysite/templates/Includes/Footer.ss`
   * `www$ echo "" > mysite/templates/Layout/Page.ss`

 * Copy the standard editor css to mysite and create the default
   project css files:
   * `www$ cp patchwork/css/editor.css mysite/css/`
   * `www$ echo "" > mysite/css/layout.css`
   * `www$ echo "" > mysite/css/form.css`
   * `www$ echo "" > mysite/css/typography.css`

 * Visit http://www.yoursite.example.com/dev/build?flush=all to rebuild the
   manifest and database (or import `patchwork/scripts/sql/DevEnv.sql` to
   a blank database and rebuild the manifest and template cache if necessary).

 * Visit http://www.yoursite.example.com/dev/test/module/patchwork to make
   sure that everything works as it should.

 * Visit http://www.yoursite.example.com/ and verify that you can see
   the example content from SilverStripe.

You should now be ready to start the actual work on the project.

## Usage Overview

patchwork provides a bunch of features which are very nice to have,
you can find some simple example for the most interesting parts below.

### Constraints for DataObjects.

Constraints can be specified for has_one-relations for DataObjects.
It is possible to use constraints for Versioned DataObjects too, provided
that both sides of the relation are Versioned and that only the stages
"Stage" and "Live" are used.

		class PageWidget extends DataObject {
			private static $db = array(
				'Title' => 'Text',
			);
			private static $has_one = array(
				'Page' => 'Page',
			);
			private static $constraints = array(
				'Page' => 'on delete cascade'
			);
        }

### Automatic publishing and unpublishing for related Versioned DataObjects.

Making sure that Versioned DataObjects connected to for example a Page
through a has_one-relation are published and unpublished when their
referenced Page will usually require quite a bit of code. Autoversioned
allows Versioned DataObjects sync their staging with their owner.

		class PageWidget extends DataObject {
			private static $db = array(
				'Title' => 'Text',
			);
			private static $has_one = array(
				'Page' => 'Page',
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
        }

### A simple scheduled job abstraction for delayed execution of tasks.

It is often useful to be able to delay the execution of certain things. To
make this possible patchwork provides `ScheduledJob` and `DeferrableBuildTask`.
All deferrable tasks should inherit `DeferrableBuildTask` and they are
scheduled through `ScheduledJob::register(...)`.

`StagePageTask` and `UpdateMetaLabelsTask` are two examples on how deferrable
tasks can be implemented.

Remember to set up a cron tab entry for patchwork so that the scheduled jobs
will actually run. It is possible to run ScheduledJobTask either through a
HTTP request or using php-cli.

 * HTTP request: `wget http://www.example.com/dev/tasks/ScheduledJobTask`
 * Command line: `php framework/cli-script.php dev/tasks/ScheduledJobTask`

### Basic CAPTCHA fields for forms and userforms.

A few simple CAPTCHA fields are available. Do note that they are very
simplistic and not very accessible.

### A generalization of the PageHolder concept.

Every SilverStripe developer will be familiar with the PageHolder concept
which is quite common in SilverStripe. An example could be:

 * `NewsHolder`
   * `NewsPage`
   * `NewsPage`
   * `NewsPage`

This solution is very neat but comes with a few important limitations.
Usually it is only possible to create NewsPages under NewsHolder, which
can create problems when you want to do something out of the ordinary,
like for example have a userform as the latest news article. And even
if NewsHolder is updated to accept more children, there could arise issues
if the PageTypes lacks the necessary fields to display the summary on
NewsHolder.

patchwork aims to solve these kind of problems by introducing `PageAggregate`,
which is basically a PageHolder which can find and display all kinds of
Page types. `PageAggregate` is quite powerful and can work with the SiteTree,
tags and categories or a specific search term to find the pages it should
display. It can handle news sections and blogs out-of-the-box.

### Carousels made easy

`PageContentItem` makes implementing carousels and similar functionality
a breeze.

		class Page extends SiteTree {
			private static $has_many = array(
				'CarouselItems' => 'PageCarouselItem'
			);
			public function getCMSFields() {
				$fields = parent::getCMSFields();
				PageCarouselItem::addCMSFieldsTo($this, $fields, $this->CarouselItems());
				return $fields;
			}
		}
		class PageCarouselItem extends PageContentItem {
		}

It is then possible to use `PageCarousel.ss` in the template to render
a standard Bootstrap carousel.

		<% include PageCarousel ID='my-cool-carousel', Items=$CarouselItems %>

### Template goodies

patchwork provides a number useful template methods.

 * `$EnvironmentType` - get the current environment type (dev, test or live).
 * `$DateCacheBuster` - add to an URL to get a time() based cache buster.
 * `$MtimeCacheBuster` - get a file_mtime cache buster for a file.
 * `$Year` - get the current year.
 * `$Copyright` - print a copyright statement (including the current year).
 * `$PatchworkNavigator` - a slightly modified version of the default
   SilverStripe navigator.

### Custom javascript for specific page types

It is often necessary to write javascript specific for a certain page type
which does not have to be executed unless the page type is relevant. To make
this trivial, patchwork will try to execute two callbacks for each page load:

 * `function PageType_ready($) { ... }`
 * `function PageType_load($) { ... }`

`PageType_ready` will be called on `$(document).ready()`, `PageType_load` will
be called on `$(window).load()`. `PageType` should obviously be replaced with
the name of the page type. For example, if we have `HomePage`, then the
callbacks would be `HomePage_ready` and `HomePage_load`. It is not necessary to
implement empty callbacks for each page type, the callback is only called if
it exists.

