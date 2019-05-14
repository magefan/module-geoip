<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Magefan\GeoIp\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind
     */
    private $maxMind;
    /**
     * UpgradeData constructor.
     * @param \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
     */
    public function __construct(
        \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
    ) {
        $this->maxMind = $maxMind;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.7', '<')) {
            $this->updateMaxMind($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateMaxMind(ModuleDataSetupInterface $setup)
    {
        if (!$this->maxMind->update()) {
            $this->maxMind->install();
        }
    }
}
