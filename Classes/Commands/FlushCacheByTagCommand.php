<?php
declare(strict_types=1);
namespace Lolli\Enetcache\Command;

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
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Controller\CommandLineController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
  * Description
  */
class FlushCacheByTagCommand extends CommandLineController {

    /**
     * Constructor
     */
    public function __construct()
    {
        // Running parent class constructor
        parent::__construct();
        // Adding options to help archive:
        // Setting help texts:
        $this->cli_help['name'] = 'enetcache flush cache entries by tags';
        $this->cli_help['description'] = 'Flush cache by given keys';
        $this->cli_help['examples'] = '/.../cli_dispatch.phpsh enetcache dropTags foo,bar';
        $this->cli_help['synopsis'] = '###OPTIONS###';
        $this->cli_help['author'] = 'Christian Kuhn';
    }

    /**
     * CLI engine
     */
    public function cli_main()
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
     * @param string $tags comma separated tag list
     */
    protected function dropTags($tags = '')
    {
        // Get tag list from user input if none given as argument
        if (strlen($tags) === 0) {
            // Output
            $this->cli_echo('Enter a list of comma separated tags: ');
            // Input
            $tags = $this->cli_keyboardInput();
        }
        $tags = array_map('trim', explode(',', $tags));

        $isValid = $this->isValidTagList($tags);
        if (!$isValid) {
            $this->cli_echo($GLOBALS['LANG']->sL('LLL:EXT:enetcache/Resources/Private/Language/locallang.xml:scheduler.droptags.invalidTagList') . "\n");
            exit;
        }
        GeneralUtility::makeInstance(PluginCache::class)->drop($tags);
        $this->cli_echo("The entries with this tags where dropped: " . implode(', ',$tags) . "\n");
    }

    /**
     * Sanitize tag list
     *
     * @param array $tags Tag list
     * @return bool true if tag list validates
     */
    protected function isValidTagList(array $tags = array())
    {
        $isValid = true;
        foreach ($tags as $tag) {
            if (!preg_match(FrontendInterface::PATTERN_TAG, trim($tag))) {
                $isValid = false;
            }
        }
        return $isValid;
    }
}
