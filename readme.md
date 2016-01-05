# Site-wide content Report


This module adds a "All content, page and files from across all subsites" report in the CMS, so that
an administrator can get a quick overview of content across subsite in the site.

[![Build Status](http://img.shields.io/travis/silverstripe/silverstripe-sitewidecontent-report.svg?style=flat-square)](https://travis-ci.org/silverstripe/silverstripe-sitewidecontent-report)
[![Code Quality](http://img.shields.io/scrutinizer/g/silverstripe/silverstripe-sitewidecontent-report.svg?style=flat-square)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-sitewidecontent-report)
[![Version](http://img.shields.io/packagist/v/silverstripe/sitewidecontent-report.svg?style=flat-square)](https://packagist.org/packages/silverstripe/sitewidecontent-report)
[![License](http://img.shields.io/packagist/l/silverstripe/sitewidecontent-report.svg?style=flat-square)](license.md)

## Install

```sh
$ composer require silverstripe/sitewidecontent-report
```
You'll then need to visit your site with `?flush=1` in the url

## Subsites Support

If the [Subsites](https://github.com/silverstripe/silverstripe-subsites) module is installed
then an additional column will be added, allowing you to see which subsites this user 
can edit pages on.

To edit the permission to check for when filtering these subsites, you can update the
`Member.subsite_description_permission` config to any other permission. By default this
is set to `SITETREE_EDIT_ALL`.

## Documentation

See the [docs/en](docs/en/index.md) folder.

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.

## Reporting Issues

Please [create an issue](https://github.com/silverstripe/sitewidecontent-report/issues) for any bugs you've found, or features you're missing.