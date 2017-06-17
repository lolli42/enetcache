<?php
namespace Lolli\Enetcache;

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

/**
 * Describes methods hooks must implement if hooking into FrontendCache::class,
 * see that class for details on what the methods do and provide.
 */
interface PluginCacheHookInterface
{
    public function get($params);
    public function set(&$params);
    public function flush();
    public function drop(&$params);
}
