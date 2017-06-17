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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Description
 */
class FlushCacheByTagCommand extends Command
{

    public function configure()
    {
        $this->setHelp('NAME:
    enetcache flush cache entries by tags

DESCRIPTION:
    Flush cache by given keys

EXAMPLES:
    - several tags as comma separated list with spaces need to be passed in quotation marks
    ./typo3/sysext/core/bin/typo3 enetcache:dropTags \'foo, bar\'
    - no spaces allowed for non quoted input
    ./typo3/sysext/core/bin/typo3 enetcache:dropTags foo,bar
    - single tag
    ./typo3/sysext/core/bin/typo3 enetcache:dropTags foo

LICENSE:
    GNU GPL - free software!

AUTHOR:
    Christian Kuhn
');
        $this->setDescription('Flush cache by given keys');
        $this->addArgument('tags', InputOption::VALUE_REQUIRED, 'comma separated list of tags to be dropped.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        if ($input->hasArgument('tags') && $tags = $input->getArgument('tags')) {
            $tags = array_map('trim', explode(',', $tags));

            $isValid = $this->isValidTagList($tags);
            if (!$isValid) {
                $io->writeln("Invalid tags. Please enter a comma separated valid list of tags to drop.\n");
                exit;
            }
            GeneralUtility::makeInstance(PluginCache::class)->drop($tags);
            $io->writeln("The entries with this tags where dropped: " . implode(', ', $tags) . "\n");
        } else {
            $io->writeln('No tags have been provided. Restart the command with a list of comma separated tags to be dropped.');
        }
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
