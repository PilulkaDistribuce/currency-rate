<?php

namespace Pilulka\CurrencyRate;

use Pilulka\CurrencyRate\Exception\InvalidArgumentException;
use Pilulka\CurrencyRate\Source\CnbSource;

class CurrencyRate
{

    const CUR_CZK = 'CZK'; // Czech Republic
    const CUR_AUD = 'AUD'; // Australia
    const CUR_BRL = 'BRL'; // Brazil
    const CUR_BGN = 'BGN'; // Bulgaria
    const CUR_CNY = 'CNY'; // China
    const CUR_DKK = 'DKK'; // Denmark
    const CUR_EUR = 'EUR'; // EMU
    const CUR_PHP = 'PHP'; // Philippines
    const CUR_HKD = 'HKD'; // Hong Kong
    const CUR_HRK = 'HRK'; // Croatia
    const CUR_INR = 'INR'; // India
    const CUR_IDR = 'IDR'; // Indonesia
    const CUR_ILS = 'ILS'; // Israel
    const CUR_JPY = 'JPY'; // Japan
    const CUR_ZAR = 'ZAR'; // SAR
    const CUR_KRW = 'KRW'; // South Korea
    const CUR_CAD = 'CAD'; // Canada
    const CUR_HUF = 'HUF'; // Hungary
    const CUR_MYR = 'MYR'; // Malaysia
    const CUR_MXN = 'MXN'; // Mexico
    const CUR_XDR = 'XDR'; // MMF
    const CUR_NOK = 'NOK'; // Norway
    const CUR_NZD = 'NZD'; // New Zealand
    const CUR_PLN = 'PLN'; // Poland
    const CUR_RON = 'RON'; // Romania
    const CUR_RUB = 'RUB'; // Russia
    const CUR_SGD = 'SGD'; // Singapur
    const CUR_SEK = 'SEK'; // Sweden
    const CUR_CHF = 'CHF'; // Switzerland
    const CUR_THB = 'THB'; // Thailand
    const CUR_TRY = 'TRY'; // Turkish
    const CUR_USD = 'USD'; // USA
    const CUR_GBP = 'GBP'; // Great Britain

    /** @var RateSource */
    private $rateSource;
    /** @var string */
    private $currencyCode;

    /**
     * CurrencyRate constructor.
     * @param $rateSource
     * @param $currencyCode
     */
    public function __construct($currencyCode, RateSource $rateSource = null)
    {
        $this->setCurrencyCode($currencyCode);
        $this->setRateSource($rateSource);
    }

    public function getAvailableCurrencyCodes()
    {
        static $reflection = null;
        if (!isset($reflection)) {
            $reflection = new \ReflectionClass($this);
        }
        return array_values($reflection->getConstants());
    }

    public function getRateOf($currencyCode, \DateTime $date = null)
    {
        if (!isset($date)) {
            $date = new \DateTime();
        }
        if($date > (new \DateTime())) {
            throw new InvalidArgumentException(
                'We cannot guess future value of currency.'
            );
        }
        return $this->rateSource->rate($currencyCode, $this->currencyCode, $date);
    }

    /**
     * @param mixed $currencyCode
     */
    private function setCurrencyCode($currencyCode)
    {
        if(!in_array($currencyCode, $this->getAvailableCurrencyCodes())) {
            throw new InvalidArgumentException(
                "Undefined currency code: `{$currencyCode}`."
            );
        }
        $this->currencyCode = $currencyCode;
    }

    /**
     * @param mixed $rateSource
     */
    private function setRateSource(RateSource $rateSource = null)
    {
        if (is_null($rateSource)) {
            $rateSource = new CnbSource();
        }
        $this->rateSource = $rateSource;
    }

}
