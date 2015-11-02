<?php

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class SitewideContentReportTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = "SitewideContentReportTest.yml";

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        foreach (range(1, 5) as $i) {
            /** @var SiteTree $page */
            $page = $this->objFromFixture("Page", "page{$i}");

            if ($i <= 3) {
                $page->doPublish();
            }
        }
    }

    public function testSourceRecords()
    {
        $report = SitewideContentReport::create();
        $records = $report->sourceRecords();

        $this->assertEquals(count($records), 2, "Returns an array with 2 items, one for pages and one for files");
        $this->assertArrayHasKey("Pages", $records);
        $this->assertArrayHasKey("Files", $records);

        /** @var DataList $pages */
        $pages = $records["Pages"];

        /** @var DataList $files */
        $files = $records["Files"];

        $this->assertEquals($pages->count(), 5, "Total number of pages");
        $this->assertEquals($files->count(), 1, "Total number of files");
    }

    public function testGetCMSFields()
    {
        $report = SitewideContentReport::create();
        $fields = $report->getCMSFields();

        if (class_exists("Subsite")) {
            $field = $fields->fieldByName("AllSubsites");
            $count = count($field->getSource());

            $this->assertEquals($count, 4, "2 subsites plus 2 added options to filter by subsite");
        } else {
            $this->assertNull($fields->fieldByName("AllSubsites"));
        }
    }
}
