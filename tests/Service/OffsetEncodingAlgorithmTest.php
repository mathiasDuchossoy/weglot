<?php

namespace App\Tests\Service;

use App\Service\OffsetEncodingAlgorithm;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OffsetEncodingAlgorithmTest extends KernelTestCase
{
    /**
     * @var OffsetEncodingAlgorithm
     */
    private $algorithm;

    /**
     * @dataProvider getTexts
     * @param $offset
     * @param $text
     * @param $encoded
     */
    public function testValidEncoding($offset, $text, $encoded)
    {
        $this->algorithm->setOffset($offset);

        $this->assertEquals($encoded, $this->algorithm->encode($text));
    }

    /**
     * @return array
     */
    public function getTexts()
    {
        return [
            [0, '', ''],
            [1, '', ''],
            [1, 'a', 'b'],
            [0, 'abc def.', 'abc def.'],
            [1, 'abc def.', 'bcd efg.'],
            [2, 'z', 'B'],
            [1, 'Z', 'a'],
            [26, 'abc def.', 'ABC DEF.'],
            [78, 'ABC DEF.', 'abc def.'],
        ];
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->algorithm = self::$container->get(OffsetEncodingAlgorithm::class);
    }
}
