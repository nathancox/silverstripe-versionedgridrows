<?php

namespace NathanCox\VersionedGridRows;

use SilverStripe\Forms\GridField;
use SilverStripe\ORM\FieldType;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Core\Config;

class VersionedGridRows implements GridField\GridField_ColumnProvider
{
	/**
	 * @var string
	 * @config
	 */
	private static $mode = 'component';     // always, config, component

	/**
	 * @var boolean
	 * @config
	 */
	private static $show_published = false;

	/**
	 * @var array
	 * @config
	 */
	private static $classes = array();


	public $fieldToFlag = false;

	public function __construct($fieldToFlag = false)
	{
		$this->fieldToFlag = $fieldToFlag;
	}

	public function getColumnsHandled($gridField)
	{
		$columns = array();
		if ($this->fieldToFlag) {
			$columns[] = $this->fieldToFlag;
		}
		return $columns;
	}

	public function getColumnContent($gridField, $record, $columnName)
	{
		if ($this->fieldToFlag && $columnName == $this->fieldToFlag) {
			return VersionedGridRows::get_status_html($record);
		}
	}



	public function augmentColumns($gridField, &$columns)
	{
	}


	public function getColumnAttributes($gridField, $record, $columnName)
	{
	}


	public function getColumnMetaData($gridField, $columnName)
	{
	}


	/**
	 * Return the publication status of the record
	 * @param  DataObject $record The object this row represents.
	 * @return string         "draft", "modified" or "published"
	 */
	public static function get_status($record)
	{
		$status = false;
		if ($record->hasExtension(Versioned::class)) {
			$latestPublished = $record->latestPublished();

			if ($latestPublished === null) {
				$status = 'draft';
			} else {
				$status = ($latestPublished ? 'published' : 'modified');
			}
		}

		return $status;
	}

	/**
	 * Get the HTML for the "draft" or "modified" label
	 * @param  DataObject $record   The object this row represents.
	 * @return string
	 */
	public static function get_flag_html($record)
	{
		$output = '';
		$flag = VersionedGridRows::get_status($record);
		if ($flag && ($flag != 'published' || Config\Config::inst()->get('VersionedGridRow', 'show_published') === true)) {
			$output = ' <span class="state-marker '.$flag.'">'.ucfirst($flag).'</span>';
		}

		return $output;
	}


	/**
	 * Returns the given string with the appropriate marker appended, for use in GridField cells.
	 * @param  DataObject $record   The object this row represents.
	 * @param  string $text         The text content of the cell (eg $this->Title)
	 * @return DBField
	 */
	public static function get_column_content($record, $text = '')
	{
		return FieldType\DBField::create_field('HTMLVarchar', $text . self::get_flag_html($record));
	}


}
