<?php

namespace NathanCox\VersionedGridRows;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;

class VersionedGridRowsExtension extends Extension
{
    /**
     * GridField extension point
     */
    public function updateNewRowClasses(&$classes, $total, $index, $record)
    {
    	$mode = Config::inst()->get('VersionedGridRows', 'mode');
    	if ($mode == 'component' &&  $this->owner->getConfig()->getComponentsByType('VersionedGridRows')->Count() === 0) {
    		return;
    	}

    	if ($mode == 'config') {
    		$classesToFlag = Config::inst()->get('VersionedGridRows', 'classes');
    		if (!in_array($this->owner->getModelClass(), $classesToFlag)) {
    			return;
    		}
    	}

      $flag = VersionedGridRows::get_status($record);

      if ($flag) {
          $classes[] = $flag;
      }
    }
}
