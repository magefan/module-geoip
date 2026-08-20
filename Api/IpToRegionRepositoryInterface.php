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
     * Retrieve region code by IP address.
     *
     * @param string $ip
     * @return mixed
     */
    public function getRegionCode($ip);

    /**
     * Retrieve region code of the current visitor.
     *
     * @return mixed
     */
    public function getVisitorRegionCode();

    /**
     * Retrieve current IP.
     *
     * @return string
     */
    public function getRemoteAddress();
}
