<?php

namespace SilverStripe\SiteWideContentReport\Tests;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataList;
use SilverStripe\SiteWideContentReport\SitewideContentReport;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\SiteWideContentReport\Model\SitewideContentTaxonomy;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\ContentReview\Extensions\SiteTreeContentReview;

/**
 * Class SitewideContentReportTest
 * @package SilverStripe\SiteWideContentReport\Tests
 */
class SitewideContentReportTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'SitewideContentReportTest.yml';

    public function setUp()
    {
        // This module is made to work with subsites, but will still operate
        // without it (although presumably being of far less value).
        // The fixture includes subsite definitions, which is a problem if
        // the module isn't installed. So we'll use the same fixture without
        // the subsites definitions if this is the case.
        if (!class_exists(Subsite::class)) {
            static::$fixture_file = 'SitewideContentReportNoSubsitesTest.yml';
        }

        parent::setUp();

        foreach (range(1, 5) as $i) {
            /** @var SiteTree $page */
            $page = $this->objFromFixture('Page', "page{$i}");

            if ($i <= 3) {
                $page->publishRecursive();
            }
        }
    }

    public function testSourceRecords()
    {
        $report = SitewideContentReport::create();
        $records = $report->sourceRecords();

        $this->assertEquals(count($records), 2, 'Returns an array with 2 items, one for pages and one for files');
        $this->assertArrayHasKey('Pages', $records);
        $this->assertArrayHasKey('Files', $records);

        /** @var DataList $pages */
        $pages = $records['Pages'];

        /** @var DataList $files */
        $files = $records['Files'];

        $this->assertEquals($pages->count(), 5, 'Total number of pages');
        $this->assertEquals($files->count(), 1, 'Total number of files');
    }

    public function testGetCMSFields()
    {
        $report = SitewideContentReport::create();
        $fields = $report->getCMSFields();

        if (class_exists(Subsite::class)) {
            $field = $fields->fieldByName('AllSubsites');
            $count = count(array_filter(array_keys($field->getSource()), function ($value) {
                return is_int($value);
            }));

            $this->assertEquals(4, $count, '2 subsites plus 2 added options to filter by subsite');
        } else {
            $this->assertNull($fields->fieldByName('AllSubsites'));
        }
    }

    public function testReportFields()
    {
        $report = SitewideContentReport::create();

        // Test pages view
        $gridField = $report->getReportField('Pages');

        /* @var $columns GridFieldDataColumns */
        $columns = $gridField->getConfig()->getComponentByType(GridFieldDataColumns::class);
        $displayed = $columns->getDisplayFields($gridField);

        $this->assertArrayHasKey('Title', $displayed);
        $this->assertArrayHasKey('Created', $displayed);
        $this->assertArrayHasKey('LastEdited', $displayed);
        $this->assertArrayHasKey('i18n_singular_name', $displayed);
        $this->assertArrayHasKey('StageState', $displayed);

        // Use correct link
        $this->assertArrayHasKey('RelativeLink', $displayed);
        $this->assertArrayNotHasKey('AbsoluteLink', $displayed);

        if (class_exists(Subsite::class)) {
            $this->assertArrayHasKey('SubsiteName', $displayed);
        } else {
            $this->assertArrayNotHasKey('SubsiteName', $displayed);
        }

        // Export-only fields are not in display list
        $this->assertArrayNotHasKey('Terms', $displayed);
        $this->assertArrayNotHasKey('OwnerNames', $displayed);
        $this->assertArrayNotHasKey('ReviewDate', $displayed);
        $this->assertArrayNotHasKey('MetaDescription', $displayed);

        // Tests print / export field
        /* @var $export GridFieldExportButton */
        $export = $gridField->getConfig()->getComponentByType(GridFieldExportButton::class);
        $exported = $export->getExportColumns();

        // Make sure all shared columns are in this report
        $this->assertArrayHasKey('Title', $exported);
        $this->assertArrayHasKey('Created', $exported);
        $this->assertArrayHasKey('LastEdited', $exported);
        $this->assertArrayHasKey('i18n_singular_name', $exported);
        $this->assertArrayHasKey('StageState', $exported);

        // Export-only fields
        $this->assertArrayHasKey('MetaDescription', $exported);

        // Use correct link
        $this->assertArrayHasKey('AbsoluteLink', $exported);
        $this->assertArrayNotHasKey('RelativeLink', $exported);

        if (class_exists(Subsite::class)) {
            $this->assertArrayHasKey('SubsiteName', $exported);
        } else {
            $this->assertArrayNotHasKey('SubsiteName', $exported);
        }

        if (SitewideContentTaxonomy::enabled()) {
            $this->assertArrayHasKey('Terms', $exported);
        } else {
            $this->assertArrayNotHasKey('Terms', $exported);
        }

        if (class_exists(SiteTreeContentReview::class)) {
            $this->assertArrayHasKey('OwnerNames', $exported);
            $this->assertArrayHasKey('ReviewDate', $exported);
        } else {
            $this->assertArrayNotHasKey('OwnerNames', $exported);
            $this->assertArrayNotHasKey('ReviewDate', $exported);
        }
    }
}
