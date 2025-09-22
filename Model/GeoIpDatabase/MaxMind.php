<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Model\GeoIpDatabase;

use Magefan\GeoIp\Model\Config;
use Magento\Framework\Archive\Gz;
use Magento\Framework\Archive\Tar;
/**
 * Class MaxMind
 * @package Magefan\GeoIp\Model\GeoIpDatabase
 */
class MaxMind
{
    /**
     * Url
     */
    const URL = 'https://magefan.com/media/geoip/GeoLite2-Country.mmdb';

    const URL_CITY = 'https://magefan.com/media/geoip/GeoLite2-City.mmdb';

    const URL_API = 'https://download.maxmind.com/app/geoip_download';

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;
    /**
     * @var \Magento\Framework\Filesystem
     */
     protected $_file;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Gz
     */
    private $gz;

    /**
     * @var Tar
     */
    private $tar;

    /**
     * MaxMind constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Psr\Log\LoggerInterface $logger
     * @param Config $config
     * @param Gz $gz
     * @param Tar $tar
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        \Psr\Log\LoggerInterface $logger,
        Config $config,
        Gz $gz,
        Tar $tar
    ) {
        $this->_dir = $dir;
        $this->_file = $file;
        $this->_logger = $logger;
        $this->config = $config;
        $this->gz = $gz;
        $this->tar = $tar;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createDir($dirPath)
    {
        $ioAdapter = $this->_file;
        if (!is_dir($dirPath)) {
            if (!$ioAdapter->mkdir($dirPath, 0775)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Can not create folder' . $dirPath));
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update()
    {
        if ($this->config->getLicenseKey()) {
            return $this->updateByAPI();
        } else {
            return $this->updateByMagefanServer();
        }
    }
    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateByMagefanServer()
    {
        $dbPath = $this->_dir->getPath('var') . '/magefan/geoip';
        $this->createDir($dbPath);

        foreach ([self::URL, self::URL_CITY] as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Can not download GeoLite2-Country.mmdb file.'));
            }

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                throw new \Magento\Framework\Exception\LocalizedException(__('File download failed. Http code: %1.', $http_code) );
            }

            curl_close($ch);

            $urlArray = explode('/', $url);
            $output_filename = $dbPath . '/' . end($urlArray);

            $fp = fopen($output_filename, 'w');
            if (!fwrite($fp, $result)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Can not save or overwrite GeoLite2-Country.mmdb file.'));
            }
            fclose($fp);
        }

        return true;
    }

    /**
     * Get GeoIP Databse via MaxMind API
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateByAPI()
    {
        $dbPath = $this->_dir->getPath('var') . '/magefan/geoip';
        $this->createDir($dbPath);

        foreach (['GeoLite2-Country', 'GeoLite2-City'] as $file) {
            $url = self::URL_API . '?' . http_build_query([
                    'edition_id' => $file,
                    'suffix' => 'tar.gz',
                    'license_key' => $this->config->getLicenseKey()
                ]);

            $ch = curl_init($url);

            $outputFilename = $dbPath . DIRECTORY_SEPARATOR . $file . '.tar.gz';
            $fp = fopen($outputFilename, 'wb');

            curl_setopt_array($ch, array(
                CURLOPT_HTTPGET => true,
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_FILE => $fp,
            ));

            $response = curl_exec($ch);

            if (!$response) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Can not download ' . $file . '.tar.gz archive.')
                );
            }

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('File download failed. Http code: %1. Please check the license key.', $http_code)
                );
            }

            curl_close($ch);

            $unpackGz = $this->gz->unpack($outputFilename, $dbPath . DIRECTORY_SEPARATOR);
            $unpackTar = $this->tar->unpack($unpackGz, $dbPath . DIRECTORY_SEPARATOR);
            $dir = $this->_file->getDirectoriesList($unpackTar);
            $this->_file->mv($dir[0] . '/' . $file . '.mmdb', $unpackTar . $file . '.mmdb');

            $this->_file->open(['path' => $unpackTar]);
            $list = $this->_file->ls();
            $this->_file->close();

            foreach ($list as $info) {
                if (!in_array($info['text'], ['GeoLite2-Country.mmdb', 'GeoLite2-City.mmdb'])) {
                    if (isset($info['id'])) {
                        $this->_file->rmdirRecursive($info['id']);
                    }
                    $this->_file->rm($info['text']);
                }
            }

            fclose($fp);
        }

        return true;
    }
}
