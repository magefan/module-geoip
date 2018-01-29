<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
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
     * [getCountryCode description]
     * @param  string $ip
     * @return string | false
     */
    public function getCountryCode($ip)
    {
        if (!isset($this->ipToCountry[$ip])) {
            $this->ipToCountry[$ip] = false;
            if (function_exists('geoip_country_code_by_name')) {
                $this->ipToCountry[$ip] = geoip_country_code_by_name($ip);
            }
            if (!$this->ipToCountry[$ip]) {
                $longIp = ip2long($ip);
                $collection = $this->ipToCountryCollectionFactory->create();
                $collection->addFieldToFilter('ip_from', ["gteq" => $longIp])
                    ->addFieldToFilter('ip_to', ["lteq" => $longIp])
                    ->setPageSize(1);
                $ipInfo = $collection->getFirstItem();
                $this->ipToCountry[$ip] = $ipInfo->getCountryCode() ?: false;
            }
        }

        return $this->ipToCountry[$ip];
    }

    /**
     * Retrieve current visitor country code by IP
     * @return string | false
     */
    public function getVisitorCountryCode()
    {
        return $this->getCountryCode(
            $this->remoteAddress->getRemoteAddress()
        );
    }
}
