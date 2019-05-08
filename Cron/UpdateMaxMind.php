<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\GeoIp\Cron;

/**
 * Class UpdateMaxMind
 * @package Magefan\GeoIp\Cron
 */
class UpdateMaxMind
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * UpdateMaxMind constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->_dir = $dir;
    }

    /**
     * Execute Cron UpdateMaxMind
     */
    public function execute()
    {
        //Magefan\GeoIp\Model\GeoIpDatabase\MaxMind::update
        return true;
    }
}
