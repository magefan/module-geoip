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
     * @var \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind
     */
    protected $maxMind;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * UpdateMaxMind constructor.
     * @param \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->maxMind = $maxMind;
        $this->_logger = $logger;
    }

    /**
     * Execute Cron UpdateMaxMind
     */
    public function execute()
    {
        try {
            $this->maxMind->update();
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            return false;
        }
        return true;
    }
}
