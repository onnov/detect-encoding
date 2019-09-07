<?php
/**
 * User: onnov
 * Date: 02.09.2019
 * Time: 18:25
 */

namespace Onnov\DetectEncoding;

/**
 * Class CodePage
 * @package Onnov\DetectEncoding
 */
class CodePage
{
    /**
     * Method to get a custom encoding range
     *
     * @param string $uppercaseLetters
     * @param string $lowercaseLetters
     * @param string $encoding
     * @return array<string, array<string, string>>
     */
    public function getRange($uppercaseLetters, $lowercaseLetters, $encoding)
    {
        return [
            $encoding => [
                'upper' => $this->getRangeStr($this->getLetterArr($uppercaseLetters, $encoding)),
                'lower' => $this->getRangeStr($this->getLetterArr($lowercaseLetters, $encoding)),
            ],
        ];
    }

    /**
     * @param array $array
     * @return string
     */
    private function getRangeStr(array $array)
    {
        $ranges = [];
        $last = null;
        foreach ($array as $current) {
            if ($current > $last + 1) {
                $lastKey = array_key_last($ranges);
                if (null !== $lastKey) {
                    $ranges[$lastKey][1] = $last;
                }
                $ranges[] = [$current, null];
            }
            $last = $current;
        }
        $ranges[array_key_last($ranges)][1] = $last;

        $stringIntervals = [];
        foreach ($ranges as $interval) {
            $stringIntervals[] = $interval[0] < $interval[1] ? implode('-', $interval) : array_pop($interval);
        }
        $string = implode(', ', $stringIntervals);

        return $string;
    }

    /**
     * @param string $strLetters
     * @param string $encoding
     * @return array<int, int|string>
     */
    private function getLetterArr(&$strLetters, $encoding)
    {
        $str = iconv('utf-8', $encoding . '//IGNORE', $strLetters);
        if (!is_string($str)) {
            return [];
        }

        $arr = array_keys(count_chars($str, 1));
        sort($arr);

        return $arr;
    }
}
