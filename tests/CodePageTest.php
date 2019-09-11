<?php

namespace Onnov\DetectEncoding\Tests;

use Onnov\DetectEncoding\CodePage;
use Onnov\DetectEncoding\EncodingDetector;
use PHPUnit\Framework\TestCase;

class CodePageTest extends TestCase
{
    public function testInstance()
    {
        $codePage = new CodePage();
        $this->assertInstanceOf(CodePage::class, $codePage);
    }

    public function testGetRange()
    {
        $cyrillicUppercase = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФЧЦЧШЩЪЫЬЭЮЯ';
        $cyrillicLowercase = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
        $codePage = new CodePage();

        $this->assertSame(
            [
                EncodingDetector::IBM866 => [
                    'upper' => '128-148, 150-159, 240',
                    'lower' => '160-175, 224-239, 241',
                ]
            ],
            $codePage->getRange(
                $cyrillicUppercase,
                $cyrillicLowercase,
                EncodingDetector::IBM866
            )
        );

        $this->assertSame(
            [
                EncodingDetector::KOI8_R => [
                    'upper' => '179, 224-231, 233-255',
                    'lower' => '163, 192-223'
                ]
            ],
            $codePage->getRange(
                $cyrillicUppercase,
                $cyrillicLowercase,
                EncodingDetector::KOI8_R
            )
        );
    }
}
