<?php

namespace SilverStripe\SiteWideContentReport\Model;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Taxonomy\TaxonomyTerm;

/**
 * Provides taxonomy integration for sitewide content report.
 *
 * Requires https://github.com/silverstripe-labs/silverstripe-taxonomy
 *
 * Class SitewideContentTaxonomy
 * @package SilverStripe\SiteWideContentReport\Model
 */
class SitewideContentTaxonomy extends Extension
{
    /**
     * Name of field to get tags from.
     *
     * @config
     *
     * @var string
     */
    private static $tag_field = 'Terms';

    /**
     * Update columns to include taxonomy details.
     *
     * @param string $itemType (i.e 'Pages' or 'Files')
     * @param array  $columns  Columns
     */
    public function updateColumns($itemType, &$columns)
    {
        if ($itemType !== 'Pages') {
            return;
        }

        // Check if pages has the tags field
        if (!self::enabled()) {
            return;
        }

        // Set column
        $field = Config::inst()->get(__CLASS__, 'tag_field');
        $columns['Terms'] = [
            'printonly' => true, // Hide on page report
            'title' => _t('SilverStripe\\SiteWideContentReport\\SitewideContentReport.Tags', 'Tags'),
            'datasource' => function ($record) use ($field) {
                $tags = $record->$field()->column('Name');

                return implode(', ', $tags);
            },
        ];
    }

    /**
     * Check if this field is enabled.
     *
     * @return bool
     */
    public static function enabled()
    {
        if (!class_exists(TaxonomyTerm::class)) {
            return false;
        }

        // Check if pages has the tags field
        $field = Config::inst()->get(__CLASS__, 'tag_field');

        return singleton('Page')->hasMethod($field);
    }
}
