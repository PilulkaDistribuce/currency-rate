<?php

namespace Pilulka\CurrencyRate;

interface RateSource
{

    public function rate($currencyFrom, $currencyTo, \DateTime $date);

}
