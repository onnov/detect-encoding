<?php

namespace Onnov\DetectEncoding\Tests;

use Generator;
use Onnov\DetectEncoding\EncodingDetector;
use PHPUnit\Framework\TestCase;

class EncodingDetectorTest extends TestCase
{
    public function testInstance()
    {
        $encodingDetector = new EncodingDetector();
        self::assertInstanceOf(EncodingDetector::class, $encodingDetector);
    }

    /**
     * @dataProvider textDataProvider
     * @param string $text
     */
    public function testGetEncoding($text)
    {
        /** correct detecting */
        $encodingDetector = new EncodingDetector();
        $encodingDetector->enableEncoding(
            [
                EncodingDetector::IBM866,
                EncodingDetector::MAC_CYRILLIC,
            ]
        );

        self::assertEquals(EncodingDetector::UTF_8, $encodingDetector->getEncoding($text));
        $textWindows1251 = iconv(EncodingDetector::UTF_8, EncodingDetector::WINDOWS_1251, $text);
        self::assertEquals(EncodingDetector::WINDOWS_1251, $encodingDetector->getEncoding($textWindows1251));
        $textISO88595 = iconv(EncodingDetector::UTF_8, EncodingDetector::ISO_8859_5, $text);
        self::assertEquals(EncodingDetector::ISO_8859_5, $encodingDetector->getEncoding($textISO88595));
        $textKOI8R = iconv(EncodingDetector::UTF_8, EncodingDetector::KOI8_R, $text);
        self::assertEquals(EncodingDetector::KOI8_R, $encodingDetector->getEncoding($textKOI8R));
        $textIBM866 = iconv(EncodingDetector::UTF_8, EncodingDetector::IBM866, $text);
        self::assertEquals(EncodingDetector::IBM866, $encodingDetector->getEncoding($textIBM866));
        $textMACCYRILLIC = iconv(EncodingDetector::UTF_8, EncodingDetector::MAC_CYRILLIC, $text);
        self::assertEquals(EncodingDetector::MAC_CYRILLIC, $encodingDetector->getEncoding($textMACCYRILLIC));
    }

    public function testAddEncoding()
    {
        $encodingDetector = new EncodingDetector();
        $encodingDetector->addEncoding(['custom' => ['upper' => '1-50,200-250,253', 'lower' => '55-100,120-180,199']]);
        self::assertArrayHasKey('custom', $encodingDetector->getEncodingList());
    }

    /**
     * @dataProvider encodingDataProvider
     * @param string|null $encoding
     */
    public function testDisableEncoding($encoding)
    {
        $encodingDetector = new EncodingDetector();
        $encodingDetector->disableEncoding([$encoding]);
        self::assertArrayNotHasKey(
            $encoding === null ? '' : $encoding,
            $encodingDetector->getEncodingList()
        );
    }

    /**
     * @dataProvider encodingDataProvider
     * @param string|null $encoding
     */
    public function testEnableEncoding($encoding)
    {
        $encodingDetector = new EncodingDetector();
        $encodingDetector->enableEncoding([$encoding]);
        $expectedEncodings = [EncodingDetector::WINDOWS_1251, EncodingDetector::KOI8_R, EncodingDetector::ISO_8859_5];
        if (!in_array($encoding, array_merge($expectedEncodings, [EncodingDetector::UTF_8, 'unknown', null]))) {
            $expectedEncodings[] = $encoding;
        }
        self::assertSame(
            $expectedEncodings,
            array_keys($encodingDetector->getEncodingList())
        );
    }

    /**
     * @dataProvider textDataProvider
     * @param string $textUtf8
     */
    public function testIconvXtoEncoding($textUtf8)
    {
        $encodingDetector = new EncodingDetector();
        self::assertSame($textUtf8, $encodingDetector->iconvXtoEncoding($textUtf8));
        $textKOI8R = iconv(EncodingDetector::UTF_8, EncodingDetector::KOI8_R, $textUtf8);
        self::assertSame(
            $textKOI8R,
            $encodingDetector->iconvXtoEncoding($textUtf8, '//IGNORE', EncodingDetector::KOI8_R)
        );
        self::assertSame(
            $textKOI8R,
            $encodingDetector->iconvXtoEncoding($textKOI8R, '//IGNORE', EncodingDetector::KOI8_R)
        );
    }

    /**
     * @dataProvider textDataProvider
     * @param string $textUtf8
     */
    public function testIconvXtoEncodingException($textUtf8)
    {
        $this->expectException('RuntimeException');

        $encodingDetector = new EncodingDetector();
        $txt = $textUtf8 . '€';

        $encodingDetector->iconvXtoEncoding($txt, '', EncodingDetector::IBM866);
    }

    public function testGetEncodingList()
    {
        $encodingDetector = new EncodingDetector();
        self::assertSame(
            [EncodingDetector::WINDOWS_1251, EncodingDetector::KOI8_R, EncodingDetector::ISO_8859_5],
            array_keys($encodingDetector->getEncodingList())
        );
    }

    /**
     * @return Generator
     */
    public function textDataProvider()
    {
        yield ['Проверяемый текст'];
        yield ['Длинный проверяемый текст. Чушь: гид вёз кэб цапф, юный жмот съел хрящ.'];
    }

    public function encodingDataProvider()
    {
        yield [EncodingDetector::UTF_8];
        yield [EncodingDetector::WINDOWS_1251];
        yield [EncodingDetector::ISO_8859_5];
        yield [EncodingDetector::KOI8_R];
        yield [EncodingDetector::MAC_CYRILLIC];
        yield [EncodingDetector::IBM866];
        yield ['unknown'];
        yield [null];
    }
}
