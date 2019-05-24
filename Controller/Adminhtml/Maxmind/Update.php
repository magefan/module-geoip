<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\GeoIp\Controller\Adminhtml\Maxmind;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class DownloadDb
 * @package Magefan\GeoIp\Controller\Adminhtml\Ajax
 */
class Update extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magefan_GeoIp::geo_ip';
    /**
     * @var \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind
     */
    protected $maxMind;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Update constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magefan\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->maxMind = $maxMind;
        $this->messageManager = $messageManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->maxMind->update()) {
            $this->messageManager->addSuccessMessage('Database MaxMind was downloaded.');
        } else {
            $this->messageManager->addErrorMessage('Something wrong when downloading a database file.');
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
