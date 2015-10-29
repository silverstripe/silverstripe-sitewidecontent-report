# Site-wide content Report


This module adds a "All content, page and files from across all subsites" report in the CMS, so that
an administrator can get a quick overview of content across subsite in the site.

[![Build Status](http://img.shields.io/travis/silverstripe/sitewidecontent-report.svg?style=flat-square)](https://travis-ci.org/silverstripe/sitewidecontent-report)
[![Code Quality](http://img.shields.io/scrutinizer/g/silverstripe/sitewidecontent-report.svg?style=flat-square)](https://scrutinizer-ci.com/g/silverstripe/sitewidecontent-report)
[![Version](http://img.shields.io/packagist/v/silverstripe/sitewidecontent-report.svg?style=flat-square)](https://packagist.org/packages/silverstripe/sitewidecontent-report)
[![License](http://img.shields.io/packagist/l/silverstripe/sitewidecontent-report.svg?style=flat-square)](LICENSE.md)

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
