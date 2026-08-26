<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magefan\GeoIp\Model\GeoIpDatabase;

use Magefan\GeoIp\Model\Config;
use Magento\Framework\Archive\Gz;
use Magento\Framework\Archive\Tar;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;

/**
 * Downloads and updates the MaxMind GeoIP database files, either from the Magefan server
 * or, when a license key is configured, directly from the MaxMind API.
 */
class MaxMind
{
    /**
     * Magefan server URL for the GeoLite2 country database.
     */
    public const URL = 'https://magefan.com/media/geoip/GeoLite2-Country.mmdb';

    public const URL_CITY = 'https://magefan.com/media/geoip/GeoLite2-City.mmdb';

    public const URL_API = 'https://download.maxmind.com/app/geoip_download';

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
     * @var File
     */
    private $fileDriver;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * MaxMind constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Psr\Log\LoggerInterface $logger
     * @param Config $config
     * @param Gz $gz
     * @param Tar $tar
     * @param File $fileDriver
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        \Psr\Log\LoggerInterface $logger,
        Config $config,
        Gz $gz,
        Tar $tar,
        File $fileDriver,
        CurlFactory $curlFactory
    ) {
        $this->_dir = $dir;
        $this->_file = $file;
        $this->_logger = $logger;
        $this->config = $config;
        $this->gz = $gz;
        $this->tar = $tar;
        $this->fileDriver = $fileDriver;
        $this->curlFactory = $curlFactory;
    }

    /**
     * Create the GeoIP database directory if it does not exist yet.
     *
     * @param string $dirPath
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createDir($dirPath)
    {
        $ioAdapter = $this->_file;
        if (!$this->fileDriver->isDirectory($dirPath)) {
            if (!$ioAdapter->mkdir($dirPath, 0775)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Can not create folder' . $dirPath));
            }
        }
        return true;
    }

    /**
     * Update the GeoIP database using the configured source.
     *
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
     * Download the GeoIP databases from the Magefan server.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateByMagefanServer()
    {
        $dbPath = $this->_dir->getPath('var') . '/magefan/geoip';
        $this->createDir($dbPath);

        foreach ([self::URL, self::URL_CITY] as $url) {
            /** @var Curl $curl */
            $curl = $this->curlFactory->create();
            $curl->get($url);

            $result = $curl->getBody();
            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Can not download GeoLite2-Country.mmdb file.')
                );
            }

            $httpCode = $curl->getStatus();
            if ($httpCode != 200) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('File download failed. Http code: %1.', $httpCode)
                );
            }

            $urlArray = explode('/', $url);
            $outputFilename = $dbPath . '/' . end($urlArray);

            $this->fileDriver->filePutContents($outputFilename, $result);
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

            $outputFilename = $dbPath . DIRECTORY_SEPARATOR . $file . '.tar.gz';
            $fp = $this->fileDriver->fileOpen($outputFilename, 'wb');

            /** @var Curl $curl */
            $curl = $this->curlFactory->create();
            $curl->setOptions([
                CURLOPT_HTTPGET => true,
                CURLOPT_HEADER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_FILE => $fp,
            ]);
            $curl->get($url);

            $response = $curl->getBody();
            $httpCode = $curl->getStatus();

            $this->fileDriver->fileClose($fp);

            if (!$response) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Can not download ' . $file . '.tar.gz archive.')
                );
            }

            /**
             * Magento's Curl client misreports the status of a redirected request: its header
             * callback never resets between redirect hops, so getStatus() can return the
             * intermediate 30x code instead of the final one even though the file was
             * downloaded correctly. Fall back to checking the actual gzip signature so a
             * successfully downloaded archive is not rejected because of that upstream bug.
             */
            if ($httpCode != 200 && !$this->isValidGzFile($outputFilename)) {
                if ($httpCode == 429) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('MaxMind daily download limit reached. Please try again later.')
                    );
                }

                throw new \Magento\Framework\Exception\LocalizedException(
                    __('File download failed. Http code: %1. Please check the license key.', $httpCode)
                );
            }

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
        }

        return true;
    }

    /**
     * Check whether the downloaded file starts with a valid gzip signature.
     *
     * @param string $filename
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function isValidGzFile(string $filename): bool
    {
        if (!$this->fileDriver->isExists($filename)) {
            return false;
        }

        $resource = $this->fileDriver->fileOpen($filename, 'rb');
        $signature = $this->fileDriver->fileRead($resource, 2);
        $this->fileDriver->fileClose($resource);

        return $signature === "\x1f\x8b";
    }
}
