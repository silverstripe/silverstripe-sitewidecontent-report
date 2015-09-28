<?php

/**
 * Content side-report listing all pages and files from all subites
 */

class SitewideContentReport extends SS_Report {

	public function title() {
		return _t('SitewideContentReport.Title', 'Site-wide content report');
	}

	public function description() {
		return _t('SitewideContentReport.Description', 'All pages and files across all Subsites');
	}
	
	/**
	 * returns an array with 2 elements, one with a list of Page on the site (and all subsites if applicable) and another with files
	 *
	 * @return array	 
	**/
	public function sourceRecords() {

		if(class_exists('Subsite') && Subsite::get()->count() > 0) {
			$origMode = Versioned::get_reading_mode();
			Versioned::set_reading_mode('Stage.Stage');
			$items =  array('Pages' => Subsite::get_from_all_subsites('SiteTree'), 'Files' => Subsite::get_from_all_subsites('File'));
			Versioned::set_reading_mode($origMode);
			return $items;
		} else {
			return array('Pages' => Versioned::get_by_stage('SiteTree', 'Stage'), 'Files' => File::get());
		}	
		
	}

	/**
	 * returns columns for the gridfields on this report
	 *
	 * @param string $itemType, It can be 'Pages' or 'Files' depending on which GridField we are getting the columns for.
	 * @return array
	**/
	public function columns($itemType = 'Pages') {
		$columns = array(
			'Title' => array(
				"title" => _t('SitewideContentReport.Name', 'Name'),
				"link" => true,
			),
			'Created.Nice' => _t('SitewideContentReport.Created', 'Date created'),
			'LastEdited.Nice' => _t('SitewideContentReport.LastEdited', 'Date last edited')
		);

		$mainSiteLabel = _t('SitewideContentReport.MainSite', 'Main Site');
		if($itemType == 'Pages') {
			$columns['ClassName'] = _t('SitewideContentReport.PageType', 'Page type');
			$columns['StageState'] = array(
				'title' => _t('SitewideContentReport.Stage', 'Stage'),
				'formatting' => function($value, $item) {
					return ($item->isPublished()) ? _t('SitewideContentReport.Published', 'Published') : _t('SitewideContentReport.Draft', 'Draft');
				}
			);
			$columns['URLSegment'] = _t('SitewideContentReport.URL', 'URL');
		} else {
			$columns['FileType'] = _t('SitewideContentReport.FileType', 'File type');
			$columns['Size'] = _t('SitewideContentReport.Size', 'Size');
			$columns['Filename'] = _t('SitewideContentReport.Directory', 'Directory');
			$mainSiteLabel .= ' ' . _t('SitewideContentReport.AccessFromAllSubsites', '(accessible by all subsites)');
		}

		if(class_exists('Subsite') && Subsite::get()->count() > 0) {
			$columns['SubsiteName'] = array(
				'title' =>  _t('SitewideContentReport.Subsite', 'Subsite'),
				'formatting' => function($value, $item) use ($mainSiteLabel) {
					$title = ($item->Subsite()->Title) ? $item->Subsite()->Title : $mainSiteLabel;
					return sprintf('%s', $title);
				}
			);
		} 

		return $columns;
	}

	public function getCMSFields() {
		Requirements::javascript(SITEWIDE_CONTENT_REPORT . '/javascript/sitewidecontentreport.js');
		$fields = parent::getCMSFields();

		if(class_exists('Subsite')) {
			$subsites = Subsite::all_sites()->map();
			$fields->insertBefore(HeaderField::create('PagesTitle', _t('SitewideContentReport.Pages', 'Pages'), 3), 'Report-Pages');
			$fields->insertBefore(DropdownField::create('AllSubsites', _t('SitewideContentReport.FilterBy', 'Filter by:'), $subsites)
				->addExtraClass('subsite-filter')
				->setEmptyString('All Subsites')
			, 'Report-Pages');
		}
		
		$fields->push(HeaderField::create('FilesTitle', _t('SitewideContentReport.Files', 'Files'), 3));
		$fields->push($this->getReportField('Files'));
		
		return $fields;
	}

	/**
 	 * creates a GridField for pages and another one for files with different columns
 	 * GridFields have an export and a print button.
 	 *
 	 * @param string $itemType, it can be 'Pages' or 'Files'
 	 * @return GridField
	**/
	public function getReportField($itemType = 'Pages') {

		$params = isset($_REQUEST['filters']) ? $_REQUEST['filters'] : array();
		$items = $this->sourceRecords($params, null, null);
		
		$gridField = new GridFieldBasicContentReport('Report-' . $itemType,false, $items[$itemType]);

		$gridFieldConfig = GridFieldConfig::create()->addComponents(
			new GridFieldToolbarHeader(),
			new GridFieldSortableHeader(),
			new GridFieldDataColumns(),
			new GridFieldPaginator(),
			new GridFieldButtonRow('after'),
			$printButton = new GridFieldPrintButton('buttons-after-left'),
			$exportButton = new GridFieldExportButton('buttons-after-left')
		);

		$gridField->setConfig($gridFieldConfig);
		$columns = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
		

		$displayFields = array();
		$fieldCasting = array();
		$fieldFormatting = array();

		// Parse the column information
		foreach($this->columns($itemType) as $source => $info) {
			if(is_string($info)) $info = array('title' => $info);
			
			if(isset($info['formatting'])) $fieldFormatting[$source] = $info['formatting'];
			if(isset($info['csvFormatting'])) $csvFieldFormatting[$source] = $info['csvFormatting'];
			if(isset($info['casting'])) $fieldCasting[$source] = $info['casting'];

			if(isset($info['link']) && $info['link']) {
				$fieldFormatting[$source] = function($value, &$item) {
					return sprintf(
						'<a href="%s">%s</a>',
						Controller::join_links(singleton('CMSPageEditController')->Link('show'), $item->ID),
						$value
					);
				};
			}

			$displayFields[$source] = isset($info['title']) ? $info['title'] : $source;
		}
		$columns->setDisplayFields($displayFields);
		$columns->setFieldCasting($fieldCasting);
		$columns->setFieldFormatting($fieldFormatting);

		$printExportColumns = $this->getPrintExportColumns($gridField, $itemType, $columns);
		$printButton->setPrintColumns($printExportColumns);
		$exportButton->setExportColumns($printExportColumns);

		return $gridField;
	}

	/**
	 * returns the columns for the export and print functionality
	 *
	 * @param GridField, $gridField
	 * @param string, @itemType, it can be 'Pages' or 'Files'
	 * @param GridFieldDataColumns, $columns
	 * @return GridFieldDataColumns, the columns to be used for the print and export functionality
	**/
	function getPrintExportColumns($gridField, $itemType = 'Pages', $columns) {

		$displayColumns = $columns->getDisplayFields($gridField);
		unset($displayColumns['SubsiteName']);
		unset($displayColumns['StageState']);
		$displayColumns['Subsite.Title'] = _t('SitewideContentReport.Subsite', 'Subsite');
		if($itemType == 'Pages') {
			$displayColumns['isPublished'] = _t('SitewideContentReport.Stage', 'Stage');
			
		} else {
			unset($displayColumns['Stage']);
			$displayColumns['Subsite.Title'] = _t('SitewideContentReport.Subsite', 'Subsite');
		}

		if(!class_exists('Subsite')) {
			unset($displayColumns['Subsite.Title']);
		}

		return $displayColumns;
	}

}

class GridFieldBasicContentReport extends GridField {

	/**
	 * Get the value of a named field  on the given record.
	 * Use of this method ensures that any special rules around the data for this gridfield are followed.
	 */
	public function getDataFieldValue($record, $fieldName) {
		// Custom callbacks
		if(isset($this->customDataFields[$fieldName])) {
			$callback = $this->customDataFields[$fieldName];
			return $callback($record);
		}

		// Default implementation
		if($record->hasMethod('relField')) {
			$value =  $record->relField($fieldName);
			if($fieldName == 'isPublished') {
				$value = ($value) ? _t('SitewideContentReport.Published', 'Published') : _t('SitewideContentReport.Draft', 'Draft');
			} elseif($fieldName == 'Subsite.Title') {
				$value = ($value) ? $value : _t('SitewideContentReport.MainSite', 'Main Site');
			}
			return $value;
		} elseif($record->hasMethod($fieldName)) {
			return $record->$fieldName();
		} else {
			return $record->$fieldName;
		}
	}

	protected function getRowAttributes($total, $index, $record) {

		$rowClasses = $this->newRowClasses($total, $index, $record);

		return array(
			'class' => implode(' ', $rowClasses),
			'data-id' => $record->ID,
			'data-class' => $record->ClassName,
			'data-subsite-id' => $record->SubsiteID
		);
	}
}
