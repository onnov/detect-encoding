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
            [EncodingDetector::UTF_8 => ['upper' => '129, 144-164, 166-175, 208', 'lower' => '128-143, 145, 176-191, 208-209']],
            $codePage->getRange($cyrillicUppercase, $cyrillicLowercase, EncodingDetector::UTF_8)
        );

        $this->assertSame(
            [EncodingDetector::KOI8_R => ['upper' => '179, 224-231, 233-255', 'lower' => '163, 192-223']],
            $codePage->getRange($cyrillicUppercase, $cyrillicLowercase, EncodingDetector::KOI8_R)
        );

    }
}
