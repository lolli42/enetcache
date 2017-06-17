<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2013 Markus Guenther <markus.guenther@e-netconsulting.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This script offers a cli task to drop content and page caches of cached content
 * elements which are tagged with one of the given tags.
 *
 * @author Markus Guenther <markus.guenther@e-netconsulting.com>
 */
if (!defined('TYPO3_cliMode')) {
    die('You cannot run this script directly!');
}

/**
 * Drop cache entries tagged with given tags
 */
class tx_enetcache_cli extends t3lib_cli
{
    /**
     * Constructor
     *
     * @return tx_cliexample_cli
     */
    public function __construct()
    {
        // Running parent class constructor
        parent::t3lib_cli();

            // Setting help texts:
        $this->cli_help['name'] = 'enetcache drop cache entries by tags';
        $this->cli_help['synopsis'] = '###OPTIONS###';
        $this->cli_help['description'] = 'Class with basic functionality for CLI scripts';
        $this->cli_help['examples'] = '/.../cli_dispatch.phpsh enetcache dropTags foo,bar';
        $this->cli_help['author'] = 'Markus Guenther <markus.guenther@e-netconsulting.com>';
    }

    /**
     * CLI engine
     *
     * @param array Command line arguments
     * @return string
     */
    public function cli_main($argv)
    {
        // get task (function)
        $task = (string)$this->cli_args['_DEFAULT'][1];

        if (!$task) {
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        }

        if ($task === 'dropTags') {
            $this->dropTags((string)$this->cli_args['_DEFAULT'][2]);
        }
    }

    /**
     * Drop cache entries by tag
     *
     * @param string Comma separated tag list
     */
    protected function dropTags($tags = '')
    {
        // Get tag list from user input if none given as argument
        $keyboardInput = false;
        if (strlen($tags) === 0) {
            // Output
            $this->cli_echo('Enter a list of comma separated tags: ');
                // Input
            $tags = $this->cli_keyboardInput();
            $keyboardInput = true;
        }

            // @TODO: Sanitize tag list like it is done in scheduler task
        t3lib_div::makeInstance('tx_enetcache')->drop(explode(',', $tags));

        if ($keyboardInput) {
            $this->cli_echo("The entries with this tags where dropped: " . $tags . "\n");
        }
    }
}

    // Call the functionality
$cleanerObj = t3lib_div::makeInstance('tx_enetcache_cli');
$cleanerObj->cli_main($_SERVER['argv']);
