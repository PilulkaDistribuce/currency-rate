<?php

namespace Pilulka\CurrencyRate\Source;

use Pilulka\CurrencyRate\CurrencyRate;
use Pilulka\CurrencyRate\Exception\InvalidArgumentException;
use Pilulka\CurrencyRate\Exception\RuntimeException;
use Pilulka\CurrencyRate\RateSource;

class CnbSource implements RateSource
{

    const SOURCE_URL_MASK = 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/rok.txt?rok=%d';
    private $data = [];

    /**
     * @param $currencyFrom
     * @param $currencyTo
     * @param \DateTime $date
     * @return float|int
     */
    public function rate($currencyFrom, $currencyTo, \DateTime $date)
    {
        $this->validateDate($date);
        $this->loadSource($date);
        $row = $this->findDataRow($date);
        $from = $currencyFrom == CurrencyRate::CUR_CZK
            ? 1
            : $row[$currencyFrom];
        $to = $currencyTo == CurrencyRate::CUR_CZK
            ? 1
            : $row[$currencyTo];
        return $from / $to;
    }


    /**
     * @param \DateTime $date
     */
    private function loadSource(\DateTime $date)
    {
        $this->storeCacheFile($date);
        $this->loadDataForYear($date);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    private function isFileValid(\DateTime $date)
    {
        if (!file_exists($this->cacheFilePath($date))) return false;
        if ($date->format('Y') == (new \DateTime())->format('Y')) {
            return $this->validateCacheFileOfThisYear($date);
        }
        return false;
    }

    /**
     * @param \DateTime $date
     * @return bool|null
     */
    private function validateCacheFileOfThisYear(\DateTime $date)
    {
        static $validation = null; // to stop reading from disk
        if (!isset($validation)) {
            $validation = filemtime($this->cacheFilePath($date)) > (time() - 86400); // 86400 seconds in one day
        }
        return $validation;
    }

    private function cacheFilePath(\DateTime $date)
    {
        return sprintf(__DIR__ . '/../../cache/cnb_%d.txt', $date->format('Y'));
    }

    private function sourceUrl(\DateTime $date)
    {
        return sprintf(self::SOURCE_URL_MASK, $date->format('Y'));
    }

    /**
     * @param \DateTime $date
     */
    private function storeCacheFile(\DateTime $date)
    {
        if (!$this->isFileValid($date)) {
            $success = file_put_contents(
                $this->cacheFilePath($date),
                file_get_contents($this->sourceUrl($date))
            );
            if ($success === FALSE)
                throw new RuntimeException(
                    "It was to possible to write content " .
                    "of url: `{$this->sourceUrl($date)}` " .
                    "to path: `{$this->cacheFilePath($date)}`"
                );
        }
    }

    /**
     * @param \DateTime $date
     */
    private function loadDataForYear(\DateTime $date)
    {
        if (!isset($this->data[$date->format('Y')])) {
            $source = new \SplFileObject($this->cacheFilePath($date), 'r');
            $first = $source->fgetcsv('|');
            $header = [];
            foreach ($first as $i => $value) {
                if ($i === 0) continue;
                list($multiplier, $code) = explode(' ', $value);
                $header[$i] = [
                    'multiplier' => $multiplier,
                    'code' => $code,
                ];
            }
            while ($row = $source->fgetcsv('|')) {
                $date = \DateTime::createFromFormat('d.m.Y', $row[0]);
                if (!$date) break;
                $item = [];
                foreach ($row as $key => $value) {
                    if ($key === 0) continue;
                    $item[$header[$key]['code']] = floatval(str_replace(',', '.', $value)) / $header[$key]['multiplier'];
                }
                $this->data[$date->format('Y')][$date->format('z') + 1] = $item;
            }
        }
    }

    /**
     * @param \DateTime $date
     * @return array|null
     */
    private function findDataRow(\DateTime $date)
    {
        $row = null;
        $day = $date->format('z') + 1;
        foreach ($this->data[$date->format('Y')] as $dayInYear => $item) {
            if ($row && $dayInYear > $day) break;
            $row = $item;
            if ($day <= $dayInYear) {
                $day = $dayInYear;
            }
        }
        return $row;
    }

    /**
     * @param \DateTime $date
     */
    private function validateDate(\DateTime $date)
    {
        if ($date > (new \DateTime())) {
            throw new InvalidArgumentException(
                'We cannot guess future value of currency'
            );
        }
    }

}
