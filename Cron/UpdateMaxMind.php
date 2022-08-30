<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\GeoIp\Cron;

use Magefan\GeoIp\Model\Config;

/**
 * Class UpdateMaxMind
 * @package Magefan\GeoIp\Cron
 */
class UpdateMaxMind
{
    /**
     * @var \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind
     */
    protected $maxMind;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * UpdateMaxMind constructor.
     * @param \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind,
        \Psr\Log\LoggerInterface $logger,
        Config $config
    ) {
        $this->maxMind = $maxMind;
        $this->_logger = $logger;
        $this->config = $config;
    }

    /**
     * Execute Cron UpdateMaxMind
     */
    public function execute()
    {
        try {
            if ($this->config->getLicenseKey()) {
                $this->maxMind->updateAPI();
            } else {
                $this->maxMind->update();
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            return false;
        }
        return true;
    }
}
