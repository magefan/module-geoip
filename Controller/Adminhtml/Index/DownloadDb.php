<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\GeoIp\Controller\Adminhtml\Index;


/**
 * Class DownloadDb
 * @package Magefan\GeoIp\Controller\Adminhtml\Ajax
 */
class DownloadDb extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        return;
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
