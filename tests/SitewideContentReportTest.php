<?php

class SitewideContentReportTest extends SapphireTest {

	protected static $fixture_file = 'SitewideContentReportTest.yml';

	function testSourceRecords() {

		$this->objFromFixture('Page', 'page1')->doPublish();
		$this->objFromFixture('Page', 'page2')->doPublish();
		$this->objFromFixture('Page', 'page3')->doPublish();
		$this->objFromFixture('Page', 'page4');
		$this->objFromFixture('Page', 'page5');

		$report = SitewideContentReport::create();

		$records = $report->sourceRecords();
		$this->assertEquals(count($records), 2, 'Returns an array with 2 items, one for pages and one for files');
		$this->assertTrue(isset($records['Pages']));
		$this->assertTrue(isset($records['Files']));

		$Pages = $records['Pages'];
		$this->assertEquals($Pages->count(), 5, 'Total number of pages');

		$Files = $records['Files'];
		$this->assertEquals($Files->count(), 1, 'Total number of files');
	}

	function testGetCMSFields() {
		$this->objFromFixture('Page', 'page1')->doPublish();
		$this->objFromFixture('Page', 'page2')->doPublish();
		$this->objFromFixture('Page', 'page3')->doPublish();
		$this->objFromFixture('Page', 'page4');
		$this->objFromFixture('Page', 'page5');

		$report = SitewideContentReport::create();
		$fields = $report->getCMSFields();
		if(class_exists('Subsite')) {
			$dropdown = $fields->fieldByName('AllSubsites');
			$this->assertEquals(count($dropdown->getSource()), 5, '3 subsites plus 2 added options to filter by subsite');
		} else {
			$this->assertNull($fields->fieldByName('AllSubsites'));
		}
	}
}