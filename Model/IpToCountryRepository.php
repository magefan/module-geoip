<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\GeoIp\Model;

/**
 * Class IpToCountryRepository
 * @package Magefan\GeoIp\Model
 */
class IpToCountryRepository
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var ResourceModel\IpToCountry\CollectionFactory
     */
    protected $ipToCountryCollectionFactory;

    /**
     * @var array
     */
    protected $ipToCountry = [];

    /**
     * IpToCountryRepository constructor.
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param ResourceModel\IpToCountry\CollectionFactory $ipToCountryCollectionFactory
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        ResourceModel\IpToCountry\CollectionFactory $ipToCountryCollectionFactory
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->ipToCountryCollectionFactory = $ipToCountryCollectionFactory;
    }

    /**
     * @param $ip
     * @return mixed
     */
    public function getCountryCode($ip)
    {
        if (!isset($this->ipToCountry[$ip])) {
            if (function_exists('geoip_country_code_by_name')) {
                $this->ipToCountry[$ip] = geoip_country_code_by_name($ip);
            } else {
                $longIp = ip2long($ip);
                $collection = $this->ipToCountryCollectionFactory->create();
                $collection->addFielToFilter('ip_from', ["gteq" => $longIp])
                    ->addFielToFilter('ip_to', ["lteq" => $longIp])
                    ->setPageSize(1);
                $ipInfo = $collection->getFirstItem();
                $this->ipToCountry[$ip] = $ipInfo->getCountryCode() ?: false;
            }
        }

        return $this->ipToCountry[$ip];
    }

    /**
     * @return mixed
     */
    public function getVisitorCountryCode()
    {
        return $this->getCountryCode(
            $this->remoteAddress->getRemoteAddress()
        );
    }
}