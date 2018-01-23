<?php

namespace Magefan\GeoIp\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'magefan_geoip_country'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magefan_geoip_country')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'ip_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            ['nullable' => true],
            'Ip from'
        )->addColumn(
            'ip_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '64k',
            ['nullable' => true],
            'Ip to'
        )->addColumn(
            'country_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            2,
            ['nullable' => true],
            'Country Code'
        )->addColumn(
            'country_code3',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            ['nullable' => true, 'default' => null],
            'Country Code3'
        )->addIndex(
            $installer->getIdxName('magefan_geoip_country', ['ip_from']),
            ['ip_from']
        )->addIndex(
            $installer->getIdxName('magefan_geoip_country', ['ip_to']),
            ['ip_to']
        )->setComment(
            'Magefan Geo Ip Country Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
