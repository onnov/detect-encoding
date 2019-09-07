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
    public function getRange(string $uppercaseLetters, string $lowercaseLetters, string $encoding): array
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
    protected function getRangeStr(array $array): string
    {
        $string = '';
        if (count($array) > 0) {
            $fl = 0;
            $string = $array[0];
            for ($i = 1; $i < count($array); $i++) {
                if ($array[$i] - $array[$i - 1] == 1) {
                    $fl = 1;
                    continue;
                } else {
                    if ($fl == 1) {
                        $string .= "-" . $array[$i - 1] . ", " . $array[$i];
                    } else {
                        $string .= ", " . $array[$i];
                    }
                    $fl = 0;
                }
            }
            if ($fl == 1) {
                $string .= "-" . $array[0];
            }
        }

        return $string;
    }

    /**
     * @param string $strLetters
     * @param string $encoding
     * @return array<int, int|string>
     */
    protected function getLetterArr(string &$strLetters, string $encoding): array
    {
        $str = iconv('utf-8', $encoding . '//IGNORE', $strLetters);
        if (false === $str) return [];

        $arr = array_keys(count_chars($str, 1));
        sort($arr);

        return $arr;
    }
}
