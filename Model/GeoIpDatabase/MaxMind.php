<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Model\GeoIpDatabase;

/**
 * Class MaxMind
 * @package Magefan\GeoIp\Model\GeoIpDatabase
 */
class MaxMind
{
    /**
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function update()
    {
        return true;
    }
}
