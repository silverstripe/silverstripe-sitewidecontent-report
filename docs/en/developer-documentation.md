# Developer Documentation

## Customising the output columns

In order to customise the columns included in a report you can build a custom extension and apply it to the
SitewideContentReport as necessary.

The built in extensions, for instance, each add custom columns extracted from other modules, and can
be used as a base for developing further extensions:

 - `SitewideContentReview` - Integrates with the [content review](https://github.com/silverstripe/silverstripe-contentreview) module.
 - `SitewideContentSubsites` - Integrates with the [subsites](https://github.com/silverstripe/silverstripe-subsites) module.
 - `SitewideContentTaxonomy` - Integrates with the [taxonomy](https://github.com/silverstripe-labs/silverstripe-taxonomy) module.

For instance, in order to add a new Page field to the report you could add an extension similar to the below:


```php

    use SilverStripe\Core\Extension;
    use SilverStripe\SiteWideContentReport\SiteWideContentReport;

	class MyReportExtension extends Extension {
		public function updateColumns($itemType, &$columns) {
			if(itemType !== 'Pages') {
				return;
			}
			$columns["Price"] = array(
				"title" => _t(SitewideContentReport::class . ".Subsite", "Subsite"),
				"formatting" => function ($value, $item) use ($mainSiteLabel) {
					return number_format($value, 2, '.', ',');
				},
			);
		}
	}
```

The $columns array can have any number of items added, where the key of the array represents the
field name to be included.

Each item can be either a literal string (which will be used as the title), or an array that may contain
the following key => value pairs:

 * `title`: The title to use for the column header
 * `format`: A method callback which will take the raw value and original object, and return a formatted
    string.
 * `datasource`: If the value for this column isn't a direct field on the original object, a custom callback
   can be set here. Unlike `format` this callback will only take a single parameter, the original object.
 * `printonly`: Set to true if this column is only visible on the print or export-to-csv views.
 * `casting`: Specify a field type (e.g. `Text` or `Int`) in order to assist with field casting. This is not
    necessary if `formatting` is used.

## Performance considerations

### Large data sets

If your project has a large number of pages or files, you may experience server timeouts while trying to export
this report to CSV. To avoid this issue, you can either increase your server timeout limit, or you can install
and configure the [silverstripe/gridfieldqueuedexport module](https://github.com/silverstripe/silverstripe-gridfieldqueuedexport)
which allows for CSV generation to offloaded to a queued job in the background.

An example of configuring this module in your project:

```php
use SilverStripe\Core\Extension;
use SilverStripe\Forms\GridField\GridFieldComponent;
use SilverStripe\GridfieldQueuedExport\Forms\GridFieldQueuedExportButton;

class SitewideContentReportQueuedExportExtension extends Extension
{
    public function updateExportButton(GridFieldComponent &$exportButton)
    {
        $exportButton = new GridFieldQueuedExportButton('buttons-after-left');
    }
}
```

Apply the example Extension above with YAML configuration in your project:

```yaml
---
Name: queuedsitewidecontentreport
---
SilverStripe\SitewideContentReport\SitewideContentReport:
  extensions:
    - SitewideContentReportQueuedExportExtension
```
