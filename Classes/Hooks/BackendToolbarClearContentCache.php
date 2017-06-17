<?php
namespace Lolli\Enetcache\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;

/**
 * Register a new item in top toolbar to clear plugin cache
 */
class BackendToolbarClearContentCache implements ClearCacheActionsHookInterface
{

    /**
     * Clear plugin cache item in top toolbar
     * This hook method is called by the cache action (the flash in top toolbar) with click on "Clear plugin cache"
     *
     * @param array $cacheActions Given cache actions
     * @param array $optionValues Given option values
     * @return void
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        $title = 'Clear plugin cache';
        $cacheActions[] = array(
            'id' => 'clearContentCache',
            'title' => $title,
            'href' => $GLOBALS['BACK_PATH'] . 'ajax.php?ajaxID=enetcache::clearContentCache',
            'icon' => \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('extensions-enetcache-clearcontentcache', array('title' => $title)),
        );
        $optionValues[] = 'clearContentCache';
    }
}
