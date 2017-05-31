<?php

namespace Tests\Pilulka\CurrencyRate;

use Pilulka\CurrencyRate\Source\CnbSource;

class CnbSourceTest extends \PHPUnit_Framework_TestCase
{

    public function testRate()
    {
        $this->assertEquals(
            17.455,
            $this->source()->rate('AUD', 'CZK',  new \DateTime('2016-05-26'))
        );
        $this->assertEquals(
            25.882,
            $this->source()->rate('USD', 'CZK',  new \DateTime('2016-12-17'))
        );
    }

    private function source()
    {
        return new CnbSource();
    }

}

