<?php

/**
 * Provides contentreview integration for sitewide content report.
 *
 * Requires https://github.com/silverstripe/silverstripe-contentreview
 */
class SitewideContentReview extends Extension
{
    /**
     * Update columns to include subsite details.
     *
     * @param string $itemType (i.e 'Pages' or 'Files')
     * @param array  $columns  Columns
     */
    public function updateColumns($itemType, &$columns)
    {
        if ($itemType !== 'Pages') {
            return;
        }

        // {@see SiteTreeContentReview::getOwnerNames()}
        $columns['OwnerNames'] = array(
            'printonly' => true, // Hide on page report
            'title' => _t('SitewideContentReport.Reviewer', 'Reviewer'),
        );

        // {@see SiteTreeContentView::getReviewDate()}
        $columns['ReviewDate'] = array(
            'printonly' => true, // Hide on page report
            'title' => _t('SitewideContentReport.ReviewDate', 'Review Date'),
            'formatting' => function ($value, $record) {
                if ($val = $record->getReviewDate()) {
                    return $val->Nice();
                }
            },
        );
    }
}
