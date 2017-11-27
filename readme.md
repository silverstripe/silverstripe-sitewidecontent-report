# Site-wide content Report

[![Build Status](http://img.shields.io/travis/silverstripe/silverstripe-sitewidecontent-report.svg?style=flat)](https://travis-ci.org/silverstripe/silverstripe-sitewidecontent-report)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/silverstripe/silverstripe-sitewidecontent-report/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/silverstripe/silverstripe-sitewidecontent-report/?branch=master)
[![codecov](https://codecov.io/gh/silverstripe/silverstripe-sitewidecontent-report/branch/master/graph/badge.svg)](https://codecov.io/gh/silverstripe/silverstripe-sitewidecontent-report)

## Introduction

This module adds an "All content, page and files from across all subsites" report in the CMS, so that
an administrator can get a quick overview of content across subsites in the parent site.

## Requirements

 * SilverStripe ^4.0

**Note:** For a SilverStripe 3.x compatible version, please use [the 2.x release line](https://github.com/silverstripe/silverstripe-sitewidecontent-report/tree/2.0).

## Install

```sh
$ composer require silverstripe/sitewidecontent-report
```
You'll then need to visit your site with `?flush` appended to the url

## Subsites Support

If the [Subsites](https://github.com/silverstripe/silverstripe-subsites) module is installed
then an additional column will be added, allowing you to see which subsites this user
can edit pages on.

To edit the permission to check for when filtering these subsites, you can update the
`SilverStripe\Security\Member.subsite_description_permission` config to any other permission. By default this
is set to `SITETREE_EDIT_ALL`.

## Documentation

See the [docs/en](docs/en/index.md) folder.

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.

## Reporting Issues

Please [create an issue](https://github.com/silverstripe/sitewidecontent-report/issues) for any bugs you've found, or features you're missing.
