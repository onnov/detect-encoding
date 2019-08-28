<?php
/**
 * Created by PhpStorm.
 * Project: mail
 * User: sv
 * Date: 27.08.2019
 * Time: 21:59
 */

namespace Onnov\DetectEncoding;

/**
 * Class EncodingDetector
 * @package Onnov\DetectEncoding
 */
class EncodingDetector
{
    const UTF_8 = 'UTF-8';
    const CP1251 = 'CP1251';
    const WIN_1251 = 'CP1251';
    const KOI8_R = 'KOI8-R';
    const CP866 = 'IBM866';
    const IBM866 = 'IBM866';
    const ISO_8859_5 = 'ISO-8859-5';
    const MAC = 'MAC';

    private $utfLower = 7;
    private $utfUpper = 5;
    private $lowercase = 3;
    private $uppercase = 1;
    private $lastSymbol = 0;
    private $charsets = array(
            'UTF-8' => 0,
            'CP1251' => 0,
            'KOI8-R' => 0,
            'IBM866' => 0,
            'ISO-8859-5' => 0,
            'MAC' => 0,
        );

    /**
     * @param $text
     * @param $encoding
     *
     * @return false|string
     */
    public function iconvXtoEncoding($text, $encoding = self::UTF_8)
    {
        $res = $text;
        $xec = $this->getEncoding($text);
        if (!is_null($xec) && $xec !== $encoding) {
            $res = iconv($xec, $encoding, $text);
        }

        return $res;
    }

    /**
     * Определение кодировки текста
     *
     * @param string $text
     *
     * @return string|null
     */
    public function getEncoding($text)
    {
        $res = '';
        if (!empty($text)) {
            for ($a = 0; $a < strlen($text); $a++) {
                $char = ord($text[$a]);

                // non-russian characters
                if ($char < 128 || $char > 256) {
                    continue;
                }

                // UTF-8
                $this->checkRangeUTF8($char);

                // CP1251
                $this->checkRangeCP1251($char);

                // KOI8-R
                $this->checkRangeKOI8R($char);

                // IBM866
                $this->checkRangeIBM866($char);

                // ISO-8859-5
                $this->checkRangeISO88595($char);

                // MAC
                $this->checkRangeMAC($char);

                $this->lastSymbol = $char;
            }

            $res = (string)array_search(max($this->charsets), $this->charsets);
        }

        return isset($this->charsets[$res]) ? $res : null;
    }

    /**
     * @param int $char
     */
    private function checkRangeUTF8($char)
    {
        if ($this->checkRangeUTF8fUpper($char)) {
            $this->charsets['UTF-8'] += ($this->utfUpper * 2);
        }
        if ($this->checkRangeUTF8Lower($char)) {
            $this->charsets['UTF-8'] += ($this->utfLower * 2);
        }
    }

    /**
     * @param int $char
     *
     * @return bool
     */
    private function checkRangeUTF8fUpper($char)
    {
        return ($this->lastSymbol == 208)
            && ($this->checkRange($char, 143, 176)
                || $char == 129);
    }

    /**
     * @param int $char
     *
     * @return bool
     */
    private function checkRangeUTF8Lower($char)
    {
        $range1 = (($this->lastSymbol == 208)
            && ($this->checkRange($char, 175, 192)
                || $char == 145));
        $range2 = ($this->lastSymbol == 209
            && $this->checkRange($char, 127, 144));

        return ($range1 || $range2);
    }

    /**
     * @param int $char
     */
    private function checkRangeCP1251($char)
    {
        if ($this->checkRange($char, 191, 224) || $char == 168) {
            $this->charsets['CP1251'] += $this->uppercase;
        }

        if ($this->checkRange($char, 223, 256) || $char == 184) {
            $this->charsets['CP1251'] += $this->lowercase;
        }
    }

    /**
     * @param int $char
     */
    private function checkRangeKOI8R($char)
    {
        if ($this->checkRange($char, 191, 224) || $char == 163) {
            $this->charsets['KOI8-R'] += $this->lowercase;
        }
        if ($this->checkRange($char, 222, 256) || $char == 179) {
            $this->charsets['KOI8-R'] += $this->uppercase;
        }
    }

    /**
     * @param int $char
     */
    private function checkRangeIBM866($char)
    {
        if ($this->checkRange($char, 159, 176)
            || $this->checkRange($char, 223, 241)
        ) {
            $this->charsets['IBM866'] += $this->lowercase;
        }
        if ($this->checkRange($char, 127, 160) || $char == 241) {
            $this->charsets['IBM866'] += $this->uppercase;
        }
    }

    /**
     * @param int $char
     */
    private function checkRangeISO88595($char)
    {
        if ($this->checkRange($char, 207, 240) || $char == 161) {
            $this->charsets['ISO-8859-5'] += $this->lowercase;
        }
        if ($this->checkRange($char, 175, 208) || $char == 241) {
            $this->charsets['ISO-8859-5'] += $this->uppercase;
        }
    }

    /**
     * @param int $char
     */
    private function checkRangeMAC($char)
    {
        if ($this->checkRange($char, 221, 255)) {
            $this->charsets['MAC'] += $this->lowercase;
        }
        if ($this->checkRange($char, 127, 160)) {
            $this->charsets['MAC'] += $this->uppercase;
        }
    }

    /**
     * @param $num
     * @param $min
     * @param $max
     *
     * @return bool
     */
    private function checkRange($num, $min, $max)
    {
        return ($num > $min && $num < $max);
    }
}
