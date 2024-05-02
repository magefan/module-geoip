<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

declare(strict_types=1);

namespace Magefan\GeoIp\Model;

use Magefan\GeoIp\Api\IpToRegionRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir as ModuleDir;
use Magento\Store\Model\ScopeInterface;

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
    protected $directoryList;

    /**
     * @var ModuleDir
     */
    protected $moduleDir;

    /**
     * @param ScopeConfigInterface $config
     * @param RemoteAddress $remoteAddress
     * @param RequestInterface $request
     * @param DirectoryList $directoryList
     * @param ModuleDir $moduleDir
     */
    public function __construct(
        ScopeConfigInterface $config,
        RemoteAddress $remoteAddress,
        RequestInterface $request,
        DirectoryList $directoryList,
        ModuleDir $moduleDir
    )
    {
        $this->config = $config;
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
        $this->directoryList = $directoryList;
        $this->moduleDir = $moduleDir;
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getRegionCode(string $ip = ''): string
    {
        if (!$ip) {
            $ip = $this->getIp();
        }

        if (!isset($this->ipToRegion[$ip])) {
            $this->ipToRegion[$ip] = '';

            try {
                $filename = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'magefan/geoip/GeoLite2-City.mmdb';
                if (file_exists($filename)) {
                    $datFile = $filename;
                } else {
                    $datFile = $this->moduleDir->getDir('Magefan_GeoIp') . '/data/GeoLite2-City.mmdb';
                }

                $reader = new \GeoIp2\Database\Reader($datFile);
                $record = $reader->city($ip);
                if (isset($record->subdivisions[0])) {
                    $this->ipToRegion[$ip] = $record->subdivisions[0]->isoCode;
                }
            } catch (\Exception $e) {}
        }

        return $this->ipToRegion[$ip];
    }

    /**
     * @return string
     */
    private function getIp(): string
    {
        $cloudflareEnable = $this->config->getValue(self::XML_PATH_CLOUDFLARE_ENABLED, ScopeInterface::SCOPE_STORE);
        if ($cloudflareEnable) {
            return (string)$this->request->getServer('HTTP_CF_CONNECTING_IP');
        } else {
            return (string)$this->remoteAddress->getRemoteAddress();
        }
    }
}