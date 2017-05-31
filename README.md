# Currency Rate Library
Library provides general currency rate converter, It's designed to be extended 
by any available data source.

## Implemented data sources

* API of Czech National Bank (CNB)

## Implemented currencies

It's always given by defined dat source. 
CNB offers this rates: 

* CZK - Czech Republic
* AUD - Australia
* BRL - Brazil
* BGN - Bulgaria
* CNY - China
* DKK - Denmark
* EUR - EMU
* PHP - Philippines
* HKD - Hong Kong
* HRK - Croatia
* INR - India
* IDR - Indonesia
* ILS - Israel
* JPY - Japan
* ZAR - SAR
* KRW - South Korea
* CAD - Canada
* HUF - Hungary
* MYR - Malaysia
* MXN - Mexico
* XDR - MMF
* NOK - Norway
* NZD - New Zealand
* PLN - Poland
* RON - Romania
* RUB - Russia
* SGD - Singapur
* SEK - Sweden
* CHF - Switzerland
* THB - Thailand
* TRY - Turkish
* USD - USA
* GBP - Great Britain

## Example of usage

```php
use Pilulka\CurrencyRate\CurrencyRate;

$rate = new CurrencyRate(CurrencyRate::CUR_CZK); // we want to have results in czech koruna
$rate->getRateOf(CurrencyRate::CUR_EUR); // actual rate in EUR
$rate->getRateOf(CurrencyRate::CUR_EUR, new \DateTime('2010-01-29')); // rate in EUR for 2010-01-29
```

Thank you for any comments or pull request.