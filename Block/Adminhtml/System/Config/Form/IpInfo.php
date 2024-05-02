<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

declare(strict_types=1);

namespace Magefan\GeoIp\Block\Adminhtml\System\Config\Form;

use Magefan\GeoIp\Api\IpToCountryRepositoryInterface;
use Magefan\GeoIp\Api\IpToRegionRepositoryInterface;

/**
 * Admin configurations IP information block
 */
class IpInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var IpToCountryRepositoryInterface
     */
    protected $ipToCountryRepository;

    /**
     * @var IpToRegionRepositoryInterface
     */
    protected $ipToRegionRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param IpToCountryRepositoryInterface $ipToCountryRepository
     * @param IpToRegionRepositoryInterface $ipToRegionRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        IpToCountryRepositoryInterface $ipToCountryRepository,
        IpToRegionRepositoryInterface $ipToRegionRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ipToCountryRepository = $ipToCountryRepository;
        $this->ipToRegionRepository = $ipToRegionRepository;
    }

    /**
     * Return info block html
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $country =  $this->ipToCountryRepository->getVisitorCountryCode();
        if ($country == "ZZ") {
            $country = 'Undefined';
        }

        $ip = $this->ipToCountryRepository->getRemoteAddress();
        $regionId = $this->ipToRegionRepository->getVisitorRegionCode();

        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            Your IP Address: ' . $ip . '<br/>
            Country: <b>' . $country . '</b><br/>
            Region: <b>' . $regionId . '</b>
        </div>';

        return $html;
    }

}
