<?php

class VersionedGridRow extends Extension
{
	/**
	 * @var string
	 * @config
	 */
	private static $mode = 'component';		// always, config, component

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

    /**
     * GridField extension point
     */
    public function updateNewRowClasses(&$classes, $total, $index, $record)
    {
    	$mode = Config::inst()->get('VersionedGridRow', 'mode');
    	if ($mode == 'component' &&  $this->owner->getConfig()->getComponentsByType('VersionedGridRows')->Count() === 0) {
    		return;
    	}
    	if ($mode == 'config') {
    		$classesToFlag = Config::inst()->get('VersionedGridRow', 'classes');
    		if (!in_array($record->ClassName, $classesToFlag)) {
    			return;
    		}
    	}

        $flag = VersionedGridRows::get_status($record);
        if ($flag) {
            $classes[] = $flag;
        }
    }
}
