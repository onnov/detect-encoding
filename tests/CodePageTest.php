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
        $codePage = new CodePage();
        $this->assertSame(
            [EncodingDetector::UTF_8 => ['upper' => '44-45, 48-51, 53', 'lower' => '44-45, 48-50, 53, 56-57']],
            $codePage->getRange('1-50,200-250,253', '55-100,120-180,199', EncodingDetector::UTF_8)
        );

        $cyrillicUppercase = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФЧЦЧШЩЪЫЬЭЮЯ';
        $cyrillicLowercase = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
        $this->assertSame(
            [EncodingDetector::KOI8_R => ['upper' => '179, 224-231, 233-255', 'lower' => '163, 192-223']],
            $codePage->getRange($cyrillicUppercase, $cyrillicLowercase, EncodingDetector::KOI8_R)
        );

    }
}
