<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Model;

use Magento\Store\Model\ScopeInterface;

/**
 * Class IpToCountryRepository
 * @package Magefan\GeoIp\Model
 */
class IpToCountryRepository
{
    /**
     * Default path in system.xml
     */
    const XML_PATH_CLOUDFLARE_ENABLED  = 'mf_geoip/cloudflare/cloudflare_ip_enable';

    /**
     * Allow IPs path in system.xml
     */
    const XML_PATH_ALLOW_IPS  = 'mf_geoip/developer/allow_ips';

    /**
     * Simulate country path in system.xml
     */
    const XML_PATH_SIMULATE_COUNTRY  = 'mf_geoip/developer/simulate_country';

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * IpToCountryRepository constructor.
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param ResourceModel\IpToCountry\CollectionFactory $ipToCountryCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\RequestInterface $httpRequest
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        ResourceModel\IpToCountry\CollectionFactory $ipToCountryCollectionFactory,
        $config = null,
        $httpRequest = null
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->ipToCountryCollectionFactory = $ipToCountryCollectionFactory;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->config = $config ?: $objectManager->get(
            \Magento\Framework\App\Config\ScopeConfigInterface::class
        );
        $this->request = $httpRequest ?: $objectManager->get(
            \Magento\Framework\App\RequestInterface::class
        );
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

            $simulateCountry = $this->config->getValue(self::XML_PATH_SIMULATE_COUNTRY, ScopeInterface::SCOPE_STORE);
            if ($simulateCountry) {
                $allowedIPs = explode(',', $this->config->getValue(self::XML_PATH_ALLOW_IPS, ScopeInterface::SCOPE_STORE));
                foreach ($allowedIPs as $allowedIp) {
                    $allowedIp = trim($allowedIp);
                    if ($allowedIp && $allowedIp == $ip) {
                       $this->ipToCountry[$ip] = $simulateCountry;
                       return $this->ipToCountry[$ip];
                    }
                }
            }

            $cloudflareEnable = $this->config->getValue(self::XML_PATH_CLOUDFLARE_ENABLED, ScopeInterface::SCOPE_STORE);
            if ($cloudflareEnable) {
                $countryCode = $this->request->getServer('HTTP_CF_IPCOUNTRY');
                if ($countryCode) {
                    $this->ipToCountry[$ip] = $countryCode;
                }
            }

            if (!$this->ipToCountry[$ip]) {
                if (function_exists('geoip_country_code_by_name')) {
                    $rf = new \ReflectionFunction('geoip_country_code_by_name');
                    $params = $rf->getParameters();
                    if (!$params || !is_array($params) || count($params) < 2) { /* Fix for custom geoip php libraries, so 0 or 1 params */
                        try {
                            $this->ipToCountry[$ip] = geoip_country_code_by_name($ip);    
                        } catch (\Exception $e) {
                            //do nothing
                        }
                    }
                }
            }

            if (!$this->ipToCountry[$ip]) {
                try {
                    if (file_exists(realpath(dirname(__FILE__) . '/../../../../../var/magefan/geoip/GeoLite2-Country.mmdb'))) {
                        $datFile = realpath(dirname(__FILE__) . '/../../../../../var/magefan/geoip/GeoLite2-Country.mmdb');
                    } else {
                        $datFile = realpath(dirname(__FILE__) . '/../data/GeoLite2-Country.mmdb');
                    }
                    $reader = new \GeoIp2\Database\Reader($datFile);
                    $record = $reader->country($ip);

                    if ($record && $record->country && $record->country->isoCode) {
                        $this->ipToCountry[$ip] = $record->country->isoCode;
                    }
                } catch (\Exception $e) {}
            }

            if (!$this->ipToCountry[$ip]) {
                $longIp = ip2long($ip);
                $collection = $this->ipToCountryCollectionFactory->create();
                $collection->addFieldToFilter('ip_from', ["lteq" => $longIp])
                    ->addFieldToFilter('ip_to', ["gteq" => $longIp])
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
        return $this->getCountryCode($this->getRemoteAddress());
    }

    /**
     * Retrieve current IP
     * @return string
     */
    public function  getRemoteAddress()
    {
        return $this->remoteAddress->getRemoteAddress();
    }
}
