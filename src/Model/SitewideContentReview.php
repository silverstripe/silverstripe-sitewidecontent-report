<?php

namespace SilverStripe\SiteWideContentReport\Model;

use SilverStripe\Core\Extension;
use SilverStripe\SiteWideContentReport\SitewideContentReport;

/**
 * Provides contentreview integration for sitewide content report.
 *
 * @extends Extension<SitewideContentReport>
 */
class SitewideContentReview extends Extension
{
    /**
     * Update columns to include subsite details.
     *
     * @param string $itemType (i.e 'Pages' or 'Files')
     * @param array $columns Columns
     * @return mixed
     */
    public function updateColumns($itemType, &$columns)
    {
        if ($itemType !== 'Pages') {
            return;
        }

        // {@see SiteTreeContentReview::getOwnerNames()}
        $columns['OwnerNames'] = [
            'printonly' => true, // Hide on page report
            'title' => _t('SilverStripe\\SiteWideContentReport\\SitewideContentReport.Reviewer', 'Reviewer'),
        ];

        // {@see SiteTreeContentView::getReviewDate()}
        $columns['ReviewDate'] = [
            'printonly' => true, // Hide on page report
            'title' => _t('SilverStripe\\SiteWideContentReport\\SitewideContentReport.ReviewDate', 'Review Date'),
            'formatting' => function ($value, $record) {
                if ($val = $record->getReviewDate()) {
                    return $val->Nice();
                }

                return null;
            },
        ];

        return;
    }
}
