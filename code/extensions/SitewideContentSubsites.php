<?php

/**
 * Provides subsite integration for sitewide content report.
 *
 * Requires https://github.com/silverstripe/silverstripe-subsites
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
        $mainSiteLabel = _t('SitewideContentReport.MainSite', 'Main Site');
        if ($itemType !== 'Pages') {
            $mainSiteLabel .= ' '._t('SitewideContentReport.AccessFromAllSubsites', '(accessible by all subsites)');
        }

        // Add subsite name
        $columns['SubsiteName'] = array(
            'title' => _t('SitewideContentReport.Subsite', 'Subsite'),
            'datasource' => function ($item) use ($mainSiteLabel) {
                $subsite = $item->Subsite();
                if ($subsite && $subsite->exists() && $subsite->Title) {
                    return $subsite->Title;
                } else {
                    return $mainSiteLabel;
                }
            },
        );
    }

	public function updateRowAttributes($total, $index, $record, &$attributes) {
		$attributes['data-subsite-id'] = $record->SubsiteID;
	}
}
