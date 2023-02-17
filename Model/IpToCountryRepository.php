<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Model;


use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir as ModuleDir;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Class IpToCountryRepository
 * @package Magefan\GeoIp\Model
 */
class IpToCountryRepository
{
    /**
     * Default path in system.xml
     */
    const XML_PATH_CLOUDFLARE_ENABLED  = 'mfgeoip/cloudflare/cloudflare_ip_enable';

    /**
     * Allow IPs path in system.xml
     */
    const XML_PATH_ALLOW_IPS  = 'mfgeoip/developer/allow_ips';

    /**
     * Simulate country path in system.xml
     */
    const XML_PATH_SIMULATE_COUNTRY  = 'mfgeoip/developer/simulate_country';

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var array
     */
    protected $ipToCountry = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ModuleDir
     */
    private $moduleDir;

    /**
     * IpToCountryRepository constructor.
     * @param RemoteAddress $remoteAddress
     * @param DirectoryList $directoryList
     * @param ModuleDir $moduleDir
     * @param ScopeConfigInterface $config
     * @param RequestInterface $httpRequest
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        DirectoryList $directoryList,
        ModuleDir $moduleDir,
        ScopeConfigInterface $config,
        RequestInterface $httpRequest
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->directoryList = $directoryList;
        $this->moduleDir = $moduleDir;
        $this->config = $config;
        $this->request = $httpRequest;
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

            $simulateCountry = $this->config->getValue(self::XML_PATH_SIMULATE_COUNTRY, ScopeInterface::SCOPE_STORE) ?: '';
            if ($simulateCountry) {
                $allowedIPs = explode(',', $this->config->getValue(self::XML_PATH_ALLOW_IPS, ScopeInterface::SCOPE_STORE) ?: '');
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
                    $filename = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'magefan/geoip/GeoLite2-Country.mmdb';
                    if (file_exists($filename)) {
                        $datFile = $filename;
                    } else {
                        $datFile = $this->moduleDir->getDir('Magefan_GeoIp') . '/data/GeoLite2-Country.mmdb';
                    }
                    $reader = new \GeoIp2\Database\Reader($datFile);
                    $record = $reader->country($ip);

                    if ($record && $record->country && $record->country->isoCode) {
                        $this->ipToCountry[$ip] = $record->country->isoCode;
                    }
                } catch (\Exception $e) {}
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
