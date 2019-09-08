<?php

use Onnov\DetectEncoding\EncodingDetector;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * Class GetEncodingBench
 */
class GetEncodingBench
{
    /** @var EncodingDetector */
    private $encodingDetector;

    /**
     * GetEncodingBench constructor.
     */
    public function __construct()
    {
        $this->encodingDetector = new EncodingDetector();
    }


    public function textProvider()
    {
        yield ['Проверяемый текст'];
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     * @ParamProviders({"textProvider"})
     */
    public function benchGetEncoding($text)
    {
        $this->encodingDetector->getEncoding($text);
    }
}
