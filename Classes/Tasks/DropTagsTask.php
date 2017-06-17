<?php
namespace Lolli\Enetcache\Tasks;

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

use Lolli\Enetcache\PluginCache;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Scheduler task to delete cache entries by tag
 */
class DropTagsTask extends AbstractTask
{
    /**
     * @var array Tags to be dropped, given by additional fields provider
     */
    public $tags = [];

    /**
     * API Method executed by scheduler
     *
     * @return bool true
     */
    public function execute()
    {
        GeneralUtility::makeInstance(PluginCache::class)->drop($this->tags);
        return true;
    }
}
