<?php

namespace Tests\Pilulka\CurrencyRate;

use Pilulka\CurrencyRate\CurrencyRate;
use Pilulka\CurrencyRate\Exception\InvalidArgumentException;

class CurrencyRateTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAvailableCodes()
    {
        $codes = $this->rate()->getAvailableCurrencyCodes();
        $this->assertInternalType('array', $codes);
        // check company important codes
        $codesForValidation = ['EUR', 'USD', ];
        foreach ($codesForValidation as $code) {
            $this->assertTrue(
                in_array($code, $codes),
                "Currency code `{$code}` is not available."
            );
        }
    }

    public function testRatesOf()
    {
        $this->assertInternalType(
            'float',
            $this->rate()->getRateOf(CurrencyRate::CUR_EUR, new \DateTime('2010-01-29'))
        );
    }

    public function testInvalidDate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->rate()->getRateOf(CurrencyRate::CUR_EUR, new \DateTime('+1 years'));
    }

    /**
     * @return CurrencyRate
     */
    private function rate()
    {
        return new CurrencyRate(CurrencyRate::CUR_CZK);
    }

}

