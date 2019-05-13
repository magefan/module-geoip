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
     * MaxMindInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dir = $dir;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $dirList = $this->_dir->getPath('var'). '/magefan/geoip/GeoLite2-Country.mmdb';

        if (file_exists($dirList)) {
            $modified = date("F d Y.", filemtime($dirList));
        } else {
            $modified = __('DB not found.');
        }

        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
        This GeoIP extension includes GeoLite2 data created by MaxMind, available from 
        <a href="https://www.maxmind.com">https://www.maxmind.com</a>.<br/>
        Last GeoIP Data Base Update: '. $modified .'
        </div>';

        return $html;
    }
}
