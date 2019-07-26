<?php

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class SitewideContentReportTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'SitewideContentReportTest.yml';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        // Stop default page creation from occuring - just use fixtures
        Config::inst()->update(SiteTree::class, 'create_default_pages', false);

        parent::setUp();

        foreach (range(1, 5) as $i) {
            /** @var SiteTree $page */
            $page = $this->objFromFixture('Page', "page{$i}");

            if ($i <= 3) {
                $page->doPublish();
            }
        }
    }

    public function testSourceRecords()
    {
        $report = SitewideContentReport::create();
        $records = $report->sourceRecords();

        $this->assertCount(2, $records, 'Returns an array with 2 items, one for pages and one for files');
        $this->assertArrayHasKey('Pages', $records);
        $this->assertArrayHasKey('Files', $records);

        /** @var DataList $pages */
        $pages = $records['Pages'];

        /** @var DataList $files */
        $files = $records['Files'];

        $this->assertEquals(5, $pages->count(), 'Total number of pages');
        $this->assertEquals(1, $files->count(), 'Total number of files');
    }

    public function testGetCMSFields()
    {
        $report = SitewideContentReport::create();
        $fields = $report->getCMSFields();

        if (class_exists('Subsite')) {
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
        $columns = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
        $displayed = $columns->getDisplayFields($gridField);

        $this->assertArrayHasKey('Title', $displayed);
        $this->assertArrayHasKey('Created', $displayed);
        $this->assertArrayHasKey('LastEdited', $displayed);
        $this->assertArrayHasKey('i18n_singular_name', $displayed);
        $this->assertArrayHasKey('StageState', $displayed);

        // Use correct link
        $this->assertArrayHasKey('RelativeLink', $displayed);
        $this->assertArrayNotHasKey('AbsoluteLink', $displayed);

        if (class_exists('Subsite')) {
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
        $export = $gridField->getConfig()->getComponentByType('GridFieldExportButton');
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

        if (class_exists('Subsite')) {
            $this->assertArrayHasKey('SubsiteName', $exported);
        } else {
            $this->assertArrayNotHasKey('SubsiteName', $exported);
        }

        if (SitewideContentTaxonomy::enabled()) {
            $this->assertArrayHasKey('Terms', $exported);
        } else {
            $this->assertArrayNotHasKey('Terms', $exported);
        }

        if (class_exists('SiteTreeContentReview')) {
            $this->assertArrayHasKey('OwnerNames', $exported);
            $this->assertArrayHasKey('ReviewDate', $exported);
        } else {
            $this->assertArrayNotHasKey('OwnerNames', $exported);
            $this->assertArrayNotHasKey('ReviewDate', $exported);
        }
    }
}
