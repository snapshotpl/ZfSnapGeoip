<?php

/**
 * Composer scripts
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip;

use Composer\Script\Event;

class Composer
{
    const ZEND_INDEX_PATH = 'zend-index-path';

    /**
     * Run download database file
     *
     * @param \Composer\Script\Event $event
     */
    public static function downloadData(Event $event)
    {
        $options = array_merge(array(
            self::ZEND_INDEX_PATH => 'public/index.php',
        ), $event->getComposer()->getPackage()->getExtra());

        $zendIndexPath = $options[self::ZEND_INDEX_PATH];

        $cmd = sprintf('php %s %s', $zendIndexPath, 'geoip download');
        system($cmd);
    }
}
