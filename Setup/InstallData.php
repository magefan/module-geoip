<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\GeoIp\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class InstallData
 * @package Magefan\GeoIp\Setup
 */
class InstallData implements InstallDataInterface {

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resources;

    public function __construct(\Magento\Framework\App\ResourceConnection $resources)
    {
        $this->resources = $resources;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install( ModuleDataSetupInterface $setup, ModuleContextInterface $context ) {

        $connection= $this->resources->getConnection();

        $table = $connection->getTableName( 'magefan_geoip_country' );

        $dataFile = dirname(dirname(__FILE__)) . '/data/IpToCountry.php';
        $data = require $dataFile;

        $rows = [];
        $i = 0;

        foreach ($data as $item) {
            $i++;
            $rows[] = [
                'ip_from' => $item[0],
                'ip_to' => $item[1],
                'country_code' => $item[4],
                'country_code3' => $item[5],
            ];

            if ($i == 100) {
                $connection->insertMultiple($table, $rows);
                $rows = [];
                $i = 0;
            }
        }

        if ($i) {
            $connection->insertMultiple($table, $rows);
        }

    }
}