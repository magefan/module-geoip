<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Model\GeoIpDatabase;

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
     * MaxMind constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_dir = $dir;
        $this->_file = $file;
        $this->_logger = $logger;
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
    public function install()
    {
        $this->createDir($this->_dir->getPath('var') . '/magefan/geoip');
        $filePath  = $this->_dir->getPath('app') . '/code/Magefan/GeoIp/data/GeoLite2-Country.mmdb';
        $copyFileFullPath  = $this->_dir->getPath('var'). '/magefan/geoip/GeoLite2-Country.mmdb';
        $result = $this->_file->cp($filePath, $copyFileFullPath);
        return $result;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update()
    {
        $dbPath = $this->_dir->getPath('var') . '/magefan/geoip';
        $this->createDir($dbPath);
        $url = self::URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Can not download file GeoLite2-Country.mmdb'));
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Fail download file. Http code: %1', $http_code) );
        }
        curl_close($ch);

        $output_filename = $dbPath . '/' . 'GeoLite2-Country.mmdb';
        $fp = fopen($output_filename, 'w');
        if (!fwrite($fp, $result)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Can not save or overwrite file GeoLite2-Country.mmdb'));
        }
        fclose($fp);

        return true;
    }
}
