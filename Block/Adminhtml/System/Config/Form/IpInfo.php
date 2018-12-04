<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\GeoIp\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;
use Magefan\GeoIp\Model\IpToCountryRepository;

/**
 * Admin blog configurations information block
 */
class IpInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;
    protected $ip;

    /**
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Backend\Block\Template\Context $context,
        IpToCountryRepository $ip,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList       = $moduleList;
        $this->ip = $ip;
    }

    /**
     * Return info block html
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $country =  $this->ip->getVisitorCountryCode();

        if ($country == "ZZ") {
            $country = 'Undefined';
        }
        $ip = $this->ip->getRemoteAddress();

        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            Your IP Address is ' . $ip . ' <b>('.$country.').</b> Wrong coutry, please  <a href="https://magefan.com/contact" target="_blank">contact Magefan R&D team</a>.
        </div>';

        return $html;
    }

}
