<?php

/**
 * User: onnov
 * Date: 27.08.2019
 * Time: 21:59
 */

declare(strict_types=1);

namespace Onnov\DetectEncoding;

use Exception;
use RuntimeException;

/**
 * Class EncodingDetector
 *
 * @package Onnov\DetectEncoding
 */
class EncodingDetector
{
    public const LOWER_FACTOR = 3;

    public const UTF_8 = 'utf-8';
    public const WINDOWS_1251 = 'windows-1251';
    public const KOI8_R = 'koi8-r';
    public const IBM866 = 'ibm866';
    public const ISO_8859_5 = 'iso-8859-5';
    public const MAC_CYRILLIC = 'MacCyrillic';

    /** @var array<string, array<string, string>> */
    protected $rangeModel
        = [
            self::WINDOWS_1251 => [
                'upper' => '168, 192-212, 214-223',
                'lower' => '184, 224-255',
            ],
            self::KOI8_R       => [
                'upper' => '179, 224-231, 233-255',
                'lower' => '163, 192-223',
            ],
            self::ISO_8859_5   => [
                'upper' => '161, 176-196, 198-207',
                'lower' => '208-239, 241',
            ],
            self::IBM866       => [
                'upper' => '128-148, 150-159, 240',
                'lower' => '160-175, 224-239, 241',
            ],
            self::MAC_CYRILLIC => [
                'upper' => '128-148, 150-159, 221',
                'lower' => '222-254',
            ],
        ];

    /** @var mixed[] */
    protected $ranges;

    /**
     * EncodingDetector constructor.
     */
    public function __construct()
    {
        // default setting
        $this->enableEncoding(
            [
                self::WINDOWS_1251,
                self::KOI8_R,
                self::ISO_8859_5,
            ]
        );
    }

    /**
     * Method to enable encoding definition
     * Example:
     * $detector->enableEncoding([
     *      $detector::IBM866,
     *      $detector::MAC_CYRILLIC,
     * ]);
     *
     * @param mixed[] $encodingList
     */
    public function enableEncoding(array $encodingList): void
    {
        foreach ($encodingList as $encoding) {
            if (isset($this->rangeModel[$encoding])) {
                $this->ranges[$encoding]
                    = $this->getRanges($this->rangeModel[$encoding]);
            }
        }
    }

    /**
     * Method to disable encoding definition
     * Example:
     * $detector->disableEncoding([
     *      $detector::ISO_8859_5,
     * ]);
     *
     * @param string[] $encodingList
     */
    public function disableEncoding(array $encodingList): void
    {
        foreach ($encodingList as $encoding) {
            unset($this->ranges[$encoding]);
        }
    }

    /**
     * Method for adding custom encoding
     * Example:
     * $detector->addEncoding([
     *      'encodingName' => [
     *          'upper' => '1-50,200-250,253', // uppercase character number range
     *          'lower' => '55-100,120-180,199', // lowercase character number range
     *      ],
     * ]);
     *
     * @param mixed[] $ranges
     */
    public function addEncoding(array $ranges): void
    {
        foreach ($ranges as $encoding => $config) {
            if (isset($config['upper'], $config['lower'])) {
                $this->ranges[$encoding] = $this->getRanges($config);
            }
        }
    }

    /**
     * Method for converting text of an unknown encoding into a given encoding, by default in utf-8
     * optional parameters:
     * $extra = '//TRANSLIT' (default setting) , other options: '' or '//IGNORE'
     * $encoding = 'utf-8' (default setting) , other options: any encoding that is available iconv
     *
     * @param string $text
     * @param string $extra
     * @param string $encoding
     *
     * @return string
     * @throws RuntimeException
     */
    public function iconvXtoEncoding(
        string &$text,
        string $extra = '//TRANSLIT',
        string $encoding = EncodingDetector::UTF_8
    ): string {
        $res = $text;
        $xec = $this->getEncoding($text);
        if ($xec !== $encoding) {
            $msg = 'iconv returned false';
            try {
                $res = iconv($xec, $encoding . $extra, $text);
                if ($res === false) {
                    throw new RuntimeException($msg);
                }
            } catch (Exception $error) {
                $msg = $error->getMessage();
                throw new RuntimeException($msg);
            }
        }

        return $res;
    }

    /**
     * Definition of text encoding
     *
     * @param string $text
     *
     * @return string
     */
    public function getEncoding(string &$text): string
    {
        $result = $this::UTF_8;
        if ($this->isUtf($text) === false) {
            $res = [];
            $chars = count_chars($text, 1);
            foreach ($this->ranges as $encoding => $config) {
                $upc = array_intersect_key($chars, $config['upper']);
                $loc = array_intersect_key($chars, $config['lower']);
                $res[$encoding] = (array_sum($upc) + array_sum($loc) * self::LOWER_FACTOR);
            }
            asort($res);
            $result = (string)array_key_last($res);
        }

        return $result;
    }

    /**
     * UTF Encoding Definition Method
     *
     * @param string $text
     *
     * @return bool
     */
    private function isUtf(string &$text): bool
    {
        return (bool)preg_match('/./u', $text);
    }

    /**
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    private function getRanges(array $config): array
    {
        return [
            'upper' => $this->getRange($config['upper']),
            'lower' => $this->getRange($config['lower']),
        ];
    }

    /**
     * Method to convert a range from a string to an array
     *
     * @param string $str
     *
     * @return int[]
     */
    private function getRange(string &$str): array
    {
        $ranges = [];
        foreach (explode(',', $str) as $item) {
            $arr = explode('-', $item);
            if (count($arr) > 1) {
                $ranges[] = implode(',', range($arr[0], $arr[1]));
            }
        }

        return array_flip(explode(',', implode(',', $ranges)));
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getEncodingList(): array
    {
        return $this->ranges;
    }

    /**
     * @return string[][]
     */
    public function getRangeModel(): array
    {
        return $this->rangeModel;
    }
}
