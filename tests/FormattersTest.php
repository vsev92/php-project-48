<?php

namespace Differ\Tests\Formatters;

use PHPUnit\Framework\TestCase;
use Differ\Parser;
use Differ\Formatters;
use Exception;

class FormattersTest extends TestCase
{
    public function testStylishFormatter(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');


        $pathToDiffCol =  __DIR__ . '/fixtures/DiffCol';
        $diffCol = file_get_contents($pathToDiffCol);
        $diffCol = unserialize($diffCol);


        $formatted = \Differ\Formatters\getFormattedDiffCol($diffCol, 'stylish');
        $this->assertEquals($expected, $formatted);
    }


    public function testPlainFormatter(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');

        $pathToDiffCol =  __DIR__ . '/fixtures/DiffCol';
        $diffCol = file_get_contents($pathToDiffCol);
        $diffCol = unserialize($diffCol);

        $formatted = \Differ\Formatters\getFormattedDiffCol($diffCol, 'plain');
        $this->assertEquals($expected, $formatted);
    }

    public function testJsonFormatter(): void
    {
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');

        $pathToDiffCol =  __DIR__ . '/fixtures/DiffCol';
        $diffCol = file_get_contents($pathToDiffCol);
        $diffCol = unserialize($diffCol);

        $formatted = \Differ\Formatters\getFormattedDiffCol($diffCol, 'json');
        $this->assertEquals($expected, $formatted);
    }

    public function testGetFormattedDiffCol(): void
    {
        $pathToExpected =  __DIR__ . '/fixtures/ExpectedDiffCol';
        $expected = file_get_contents($pathToExpected);
        $expected = unserialize($expected);

        $pathToDiffCol =  __DIR__ . '/fixtures/DiffCol';
        $diffCol = file_get_contents($pathToDiffCol);
        $diffCol = unserialize($diffCol);

        $this->expectException(Exception::class);
        $formatted = \Differ\Formatters\getFormattedDiffCol($diffCol, 'unknown');
    }
}
