<?php
declare(strict_types=1);

namespace Lolli\Enetcache\Composer;


use Composer\Script\Event;

/**
 * Class ScriptHelper
 */
final class ScriptHelper
{
    public static function ensureExtensionStructure(Event $event): void
    {
        $composerConfigExtraSection = $event->getComposer()->getPackage()->getExtra();
        if (empty($composerConfigExtraSection['typo3/cms']['extension-key'])
            || empty($composerConfigExtraSection['typo3/cms']['web-dir'])
        ) {
            throw new \RuntimeException(
                'This script needs properties in composer.json:'
                    . '"extra" "typo3/cms" "extension-key"'
                    . ' and "extra" "typo3/cms" "extension-key"',
                1540644486
            );
        }
        $extensionKey = $composerConfigExtraSection['typo3/cms']['extension-key'];
        $webDir = $composerConfigExtraSection['typo3/cms']['web-dir'];
        $typo3confExt = __DIR__ . '/../../' . $webDir . '/typo3conf/ext';
        if (!is_dir($typo3confExt) &&
            !mkdir($typo3confExt, 0775, true) &&
            !is_dir($typo3confExt)
        ) {
            throw new \RuntimeException(
                sprintf('Directory "%s" could not be created', $typo3confExt),
                1540650485
            );
        }
        if (!is_link($typo3confExt . '/' . $extensionKey)) {
            symlink(dirname(__DIR__, 2) . '/', $typo3confExt . '/' . $extensionKey);
        }
    }
}
