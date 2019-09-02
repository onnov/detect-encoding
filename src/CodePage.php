<?php
/**
 * Created by PhpStorm.
 * Project: mail
 * User: sv
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
    protected function getRangeStr($array)
    {
        $string = '';
        if (is_array($array) && count($array) > 0) {
            $fl = 0;
            $string = $array[0];
            for ($i = 1; $i < count($array); $i++) {
                if ($array[$i] - $array[$i - 1] == 1) {
                    $fl = 1;
                    continue;
                } else {
                    if ($fl == 1) {
                        $string .= "-".$array[$i - 1].", ".$array[$i];
                    } else {
                        $string .= ", ".$array[$i];
                    }
                    $fl = 0;
                }
            }
            if ($fl == 1) {
                $string .= "-".$array[$i - 1];
            }
        }

        return $string;
    }

    /**
     * @param string $strLetters
     * @param string $encoding
     * @return array
     */
    protected function getLetterArr(&$strLetters, $encoding)
    {
        $str = iconv('utf-8', $encoding.'//IGNORE', $strLetters);
//        $arr = str_split(count_chars($str, 3));
        $arr = array_keys(count_chars($str, 1));
        sort($arr);

        return $arr;
    }

//    /**
//     * @param string $str
//     * @param string $encoding
//     * @return false|string
//     */
//    protected function urf8ToEncoding(&$str, $encoding)
//    {
//        return iconv('utf-8', $encoding.'//IGNORE', $str);
//    }
}
