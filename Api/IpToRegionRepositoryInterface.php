<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GeoIp\Api;

interface IpToRegionRepositoryInterface
{
    /**
     * @param string $ip
     * @return string
     */
    public function getRegionCode(string $ip = ''): string;
}