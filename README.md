# Magento 2 GeoIP Database Extension by Magefan

[![Total Downloads](https://poser.pugx.org/magefan/module-geoip/downloads)](https://packagist.org/packages/magefan/module-geoip)
[![Latest Stable Version](https://poser.pugx.org/magefan/module-geoip/v/stable)](https://packagist.org/packages/magefan/module-geoip)

This Magento 2 GeoIP module provides you PHP methods for getting customer country by IP, without any additional plugin for PHP.

It is used for [Magento 2 Currency Auto Switcher](https://magefan.com/magento-2-currency-switcher-auto-currency-by-country) and [Magento 2 Auto Language Switcher](https://magefan.com/magento-2-auto-language-switcher) by Magefan


## Requirements
  * Magento Community Edition 2.0.x-2.3.x or Magento Enterprise Edition 2.0.x-2.3.x

## Installation Method 1 - Installing via composer
  * Open command line
  * Using command "cd" navigate to your magento2 root directory
  * Run command: composer require magefan/module-geoip
```
composer require magefan/module-geoip
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```


## Installation Method 2 - Installing using archive
  * Install GeoIP2 PHP API (https://github.com/maxmind/GeoIP2-php).
  * Download [ZIP Archive](https://github.com/magefan/module-geoip/archive/master.zip)
  * Extract files
  * In your Magento 2 root directory create folder app/code/Magefan/GeoIp
  * Copy files and folders from archive to that folder
  * In command line, using "cd", navigate to your Magento 2 root directory
  * Run commands:
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## How To Use
```
protected $ipToCountryRepository;

public function __construct(
    \Magefan\GeoIp\Model\IpToCountryRepository $ipToCountryRepository,
    ....//other code
) {
    $this->ipToCountryRepository = $ipToCountryRepository;
    ...//other code
}

public function example() {
    $visitorCountyCode = $this->ipToCountryRepository->getVisitorCountryCode();
    $someCountryCodeByIp = $this->ipToCountryRepository->getCountryCode('104.27.164.57');
    ...//other code
}
```

## Support
If you have any issues, please [contact us](mailto:support@magefan.com)
then if you still need help, open a bug report in GitHub's
[issue tracker](https://github.com/magefan/module-geoip/issues).

## Need More Features?
Please contact us to get a quote
https://magefan.com/contact

## License
The code is licensed under [Open Software License ("OSL") v. 3.0](http://opensource.org/licenses/osl-3.0.php).

This product includes GeoLite2 data created by MaxMind, available from
<a href="https://www.maxmind.com">https://www.maxmind.com</a>.

## Originaly use this databases:
https://www.maxmind.com

http://software77.net/geo-ip/


## Other [Magento 2 Extensions](https://magefan.com/magento2-extensions) by Magefan
  * [Magento 2 Blog Extension](https://magefan.com/magento2-blog-extension)
  * [Magento 2 Blog Plus Extension](https://magefan.com/magento2-blog-extension/pricing)
  * [Magento 2 Blog Extra Extension](https://magefan.com/magento2-blog-extension/pricing)
  * [Magento 2 Login As Customer Extension](https://magefan.com/login-as-customer-magento-2-extension)
  * [Magento 2 Convert Guest to Customer Extension](https://magefan.com/magento2-convert-guest-to-customer)
  * [Magento 2 Facebook Open Graph Extension](https://magefan.com/magento-2-open-graph-extension-og-tags)
  * [Magento 2 Auto Currency Switcher Extension](https://magefan.com/magento-2-currency-switcher-auto-currency-by-country)
  * [Magento 2 Auto Language Switcher Extension](https://magefan.com/magento-2-auto-language-switcher)
  * [Magento 2 GeoIP Switcher Extension](https://magefan.com/magento-2-geoip-switcher-extension)
  * [Magento 2 YouTube Widget Extension](https://magefan.com/magento2-youtube-extension)
  * [Magento 2 Product Widget Advanced Extension](https://magefan.com/magento-2-product-widget)
  * [Magento 2 Conflict Detector Extension](https://magefan.com/magento2-conflict-detector)
  * [Magento 2 Lazy Load Extension](https://magefan.com/magento-2-image-lazy-load-extension)
  * [Magento 2 Rocket JavaScript Extension](https://magefan.com/rocket-javascript-deferred-javascript)
  * [Magento 2 CLI Extension](https://magefan.com/magento2-cli-extension)
  * [Magento Twitter Cards Extension](https://magefan.com/magento-2-twitter-cards-extension)
  * [Magento 2 Mautic Integration Extension](https://magefan.com/magento-2-mautic-extension)
  * [Magento 2 Alternate Hreflang Extension](https://magefan.com/magento2-alternate-hreflang-extension)
  * [Magento 2 Dynamic Categories](https://magefan.com/magento-2-dynamic-categories)
  * [Magento 2 CMS Display Rules Extension](https://magefan.com/magento-2-cms-display-rules-extension)
  * [Magento 2 Translation Extension](https://magefan.com/magento-2-translation-extension)
  * [Magento 2 WebP Optimized Images Extension](https://magefan.com/magento-2-webp-optimized-images)
  * [Magento 2 Zero Downtime Deployment](https://magefan.com/blog/magento-2-zero-downtime-deployment)
