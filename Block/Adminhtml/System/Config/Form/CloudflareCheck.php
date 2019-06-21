<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\GeoIp\Block\Adminhtml\System\Config\Form;

/**
 * Admin Cloudflare Check configurations information block
 */
class CloudflareCheck extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Default path in system.xml
     */
    const XML_PATH_CLOUDFLARE_ENABLED  = 'geoip/cloudflare/cloudflare_ip_enable';
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * CloudflareCheck constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $cloudflareGeolocation = $this->config->getValue(self::XML_PATH_CLOUDFLARE_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $html = '<div id="check_cloudflare" style="display:none;padding:10px;color:#9F6000;background-color:#fffbbb;border:1px solid #ddd;margin-bottom:7px;">
        You are not use <strong>Cloudflare</strong> or <strong>Cloudflare IP Geolocation</strong> is disabled in your account. Cloudflare IP Geolocation</strong> is disabled.
        <br/>
        To use Cloudflare IP Geolocation - enable the checkbox below.</div>';

        if (!$cloudflareGeolocation) {
            $html .= '<script>
        require([
            "jquery",
            "domReady!"
        ], function($){
             $("#enable_cloudflare_ip").show();
        });
        </script>';
        }
        return $html;
    }
}
