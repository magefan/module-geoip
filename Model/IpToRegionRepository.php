<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

declare(strict_types=1);

namespace Magefan\GeoIp\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir as ModuleDir;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magefan\GeoIp\Api\IpToRegionRepositoryInterface;

class IpToRegionRepository implements IpToRegionRepositoryInterface
{
    /**
     * Default path in system.xml
     */
    const XML_PATH_CLOUDFLARE_ENABLED  = 'mfgeoip/cloudflare/cloudflare_ip_enable';

    /**
     * @var array
     */
    protected $ipToRegion = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

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
     * Get Region Code by IP
     * @param string $ip
     * @return mixed
     */
    public function getRegionCode($ip)
    {
        $ip = (string)$ip;
        if (!$ip) {
           return '';
        }

        if (!isset($this->ipToRegion[$ip])) {
            $this->ipToRegion[$ip] = '';

            try {
                $filename = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'magefan/geoip/GeoLite2-City.mmdb';
                if (file_exists($filename)) {
                    $datFile = $filename;
                } else {
                    $datFile = $this->moduleDir->getDir('Magefan_GeoIp') . '/data/GeoLite2-City.mmdb';
                    //throw new \Exception('No .mmdb file');
                }

                $reader = new \GeoIp2\Database\Reader($datFile);
                $record = $reader->city($ip);
                if ($record && $record->subdivisions && isset($record->subdivisions[0])) {
                    $this->ipToRegion[$ip] = $record->subdivisions[0]->isoCode;
                }
            } catch (\Exception $e) {}
        }

        return $this->ipToRegion[$ip];
    }

    /**
     * Retrieve current visitor country code by IP
     * @return string | false
     */
    public function getVisitorRegionCode()
    {
        return $this->getRegionCode($this->getRemoteAddress());
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
