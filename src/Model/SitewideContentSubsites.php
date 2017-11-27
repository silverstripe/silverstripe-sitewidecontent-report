<?php

namespace SilverStripe\SiteWideContentReport\Model;

use SilverStripe\Core\Extension;
use SilverStripe\Subsites\Model\Subsite;

/**
 * Provides subsite integration for sitewide content report.
 *
 * Requires https://github.com/silverstripe/silverstripe-subsites
 *
 * Class SitewideContentSubsites
 * @package SilverStripe\SiteWideContentReport\Model
 */
class SitewideContentSubsites extends Extension
{
    /**
     * Update columns to include subsite details.
     *
     * @param string $itemType (i.e 'Pages' or 'Files')
     * @param array  $columns  Columns
     */
    public function updateColumns($itemType, &$columns)
    {
        // Skip single subsite setups
        if (!Subsite::get()->count()) {
            return;
        }

        // Set title
        $mainSiteLabel = _t('SilverStripe\\SiteWideContentReport\\SitewideContentReport.MainSite', 'Main Site');
        if ($itemType !== 'Pages') {
            $mainSiteLabel .= ' '._t(
                'SilverStripe\\SiteWideContentReport\\SitewideContentReport.AccessFromAllSubsites',
                '(accessible by all subsites)'
            );
        }

        // Add subsite name
        $columns['SubsiteName'] = [
            'title' => _t('SilverStripe\\SiteWideContentReport\\SitewideContentReport.Subsite', 'Subsite'),
            'datasource' => function ($item) use ($mainSiteLabel) {
                $subsite = $item->Subsite();

                if ($subsite && $subsite->exists() && $subsite->Title) {
                    return $subsite->Title;
                } else {
                    return $mainSiteLabel;
                }
            },
        ];
    }

    /**
     * @param $total
     * @param $index
     * @param $record
     * @param $attributes
     */
    public function updateRowAttributes($total, $index, $record, &$attributes)
    {
        $attributes['data-subsite-id'] = $record->SubsiteID;
    }
}
