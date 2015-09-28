# Site-wide content Report


This module adds a "All content, page and files from across all subsites" report in the CMS, so that
an administrator can get a quick overview of content across subsite in the site.

## Install

- Clone it from https://github.com/silverstripe/sitewidecontent-report.git `git clone https://github.com/silverstripe/sitewidecontent-report.git`
- Visit your site with `?flush=1` in the url

## Subsites Support

If the [Subsites](https://github.com/silverstripe/silverstripe-subsites) module is installed
then an additional column will be added, allowing you to see which subsites this user 
can edit pages on.

To edit the permission to check for when filtering these subsites, you can update the
`Member.subsite_description_permission` config to any other permission. By default this
is set to `SITETREE_EDIT_ALL`.
