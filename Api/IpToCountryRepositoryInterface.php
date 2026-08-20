<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GeoIp\Api;

interface IpToCountryRepositoryInterface
{
    /**
     * Retrieve country code by IP address.
     *
     * @param string $ip
     * @return mixed
     */
    public function getCountryCode($ip);

    /**
     * Retrieve country code of the current visitor.
     *
     * @return mixed
     */
    public function getVisitorCountryCode();

    /**
     * Retrieve current IP.
     *
     * @return string
     */
    public function getRemoteAddress();
}
