# SilverStripe patchwork Module

Patchwork can most easily be described as "things we often use at Redema
when working with SilverStripe". In short, it is part boilerplate, part
mixed utilities, extensions and third party libraries. Theoretically
speaking it would be better to split patchwork into smaller and more
logically related modules, practically speaking it saves time to only
worry about one module (maintenance, dependencies, and so on).

Disclaimer: This module is a work in progress, anything and everything may
change at any time right now.

## Maintainer Contact

Redema AB, http://redema.se/

## Requirements

 * PHP: 5.3.1+ minimum.
 * Database: MySQL 5.5+ minimum.
 * SilverStripe: 3.1+ minimum (previous versions has never been tested).

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

Write me!
