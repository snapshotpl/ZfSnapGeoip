<?php

namespace ZfSnapGeoip\Composer;

use Composer\Script\Event;

/**
 * Composer script handler
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ScriptHandler
{
    const ZEND_INDEX_PATH = 'zend-index-path';

    /**
     * Run download database file
     *
     * @param Event $event
     */
    public static function downloadData(Event $event)
    {
        $defaultOptions = [
            self::ZEND_INDEX_PATH => 'public/index.php',
            ];
        $newOptions = $event->getComposer()->getPackage()->getExtra();
        $options = array_merge($defaultOptions, $newOptions);
        $zendIndexPath = $options[self::ZEND_INDEX_PATH];
        $geoipDownloadCmd = 'geoip download';

        if ($event->getName() === 'post-install-cmd') {
            $geoipDownloadCmd .= ' --no-clobber';
        }
        $cmd = sprintf('php %s %s', $zendIndexPath, $geoipDownloadCmd);
        system($cmd);
    }

}
