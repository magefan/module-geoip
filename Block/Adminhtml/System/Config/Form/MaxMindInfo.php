<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\GeoIp\Block\Adminhtml\System\Config\Form;

/**
 * Admin geoip maxmind configurations information block
 */
class MaxMindInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;
    /**
     * @var \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind
     */
    protected $maxMind;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

    /**
     * MaxMindInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind,
        \Magento\Framework\Filesystem\Driver\File $file,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dir = $dir;
        $this->maxMind = $maxMind;
        $this->file = $file;
    }

    /**
     * Render the MaxMind database update info block.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $dirList = $this->_dir->getPath('var'). '/magefan/geoip/GeoLite2-Country.mmdb';

        if (!$this->file->isExists($dirList)) {
            try {
                $this->maxMind->update();
            } catch (\Exception $e) {
                $this->_logger->debug($e->getMessage());
            }
        }

        if ($this->file->isExists($dirList)) {
            $modified = date("F d, Y.", $this->file->stat($dirList)['mtime']);
        } else {
            $modified = __('Can not download DB.');
        }

        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
        This GeoIP extension includes GeoLite2 data created by MaxMind, available from 
        <a target="_blank" rel="nofollow  noopener" href="https://www.maxmind.com">https://www.maxmind.com</a>.<br/>
        Last GeoIP Data Base Update: <strong>'. $modified .'</strong>
        </div>';

        return $html;
    }
}
