<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\GeoIp\Model\ResourceModel\IpToCountry;

/**
 * Class Collection
 * @package Magefan\GeoIp\Model\ResourceModel\IpToCountry
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magefan\GeoIp\Model\IpToCountry', 'Magefan\GeoIp\Model\ResourceModel\IpToCountry');
    }

}