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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $countryCode = $this->getRequest()->getServer('HTTP_CF_IPCOUNTRY');
        $html = '';

        if (!$countryCode) {
            $html = '<script>
        require([
            "jquery",
            "domReady!"
        ], function($){
            $("#mfgeoip_cloudflare_cloudflare_ip_enable").change(function() {
                var val = parseInt($(this).val());
                if (val) {
                    $("#enable_cloudflare_ip").show();
                }  else {
                    $("#enable_cloudflare_ip").hide();
                }
          }).change();
        });
        </script>';
        }
        return $html;
    }
}
