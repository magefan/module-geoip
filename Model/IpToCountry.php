<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\GeoIp\Model;

/**
 * Class IpToCountry
 * @package Magefan\GeoIp\Model
 */
class IpToCountry extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Magefan\GeoIp\Model\ResourceModel\IpToCountry');
    }

}