# Site-wide content Report

[![CI](https://github.com/silverstripe/silverstripe-sitewidecontent-report/actions/workflows/ci.yml/badge.svg)](https://github.com/silverstripe/silverstripe-sitewidecontent-report/actions/workflows/ci.yml)
[![Silverstripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

## Introduction

This module adds an "All content, page and files from across all subsites" report in the CMS, so that
an administrator can get a quick overview of content across subsites in the parent site.

## Installation

```sh
composer require silverstripe/sitewidecontent-report
```

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
