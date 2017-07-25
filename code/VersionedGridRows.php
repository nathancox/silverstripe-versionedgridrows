<?php

class VersionedGridRows extends GridFieldDataColumns
{

	public $fieldToFlag = false;

	public function __construct($fieldToFlag = false)
	{
		$this->fieldToFlag = $fieldToFlag;
	}


	public function getColumnContent($gridField, $record, $columnName)
	{
		if ($this->fieldToFlag && $columnName == $this->fieldToFlag) {
			return VersionedGridRows::get_status_html($record);
		}
	}

    /**
     * Return the publication status of the record
     * @param  DataObject $record The object this row represents.
     * @return string         "draft", "modified" or "published"
     */
    public static function get_status($record)
    {
        $flag = false;
        if ($record->hasExtension('Versioned')) {
            $latestPublished = $record->latestPublished();

            if ($latestPublished === null) {
                $flag = 'draft';
            } else {
                $flag = ($latestPublished ? 'published' : 'modified');
            }
        }

        return $flag;
    }

    /**
     * Get the HTML for the "draft" or "modified" label
     * @param  DataObject $record   The object this row represents.
     * @return string
     */
    public static function get_status_html($record)
    {
        $output = '';
        $flag = VersionedGridRows::get_status($record);
        if ($flag && ($flag != 'published' || Config::inst()->get('VersionedGridRow', 'show_published') === true)) {
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
    public static function get_cell_content($record, $text)
    {
        return DBField::create_field('HTMLVarchar', $text . ' ' . self::get_status_html($record));
    }


}
