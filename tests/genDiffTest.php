<?php

namespace Differ\Tests\genDiff;

use PHPUnit\Framework\TestCase;
use Differ\Parser;
use Differ\Parser\SourceType;
use Differ\Formatters;

class GenDiffTest extends TestCase
{
    public function testDiffer(): void
    {

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'stylish');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'plain');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'json');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.yml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'stylish');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.yml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'plain');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.yml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'json');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yaml');
        $path2 = (__DIR__ . '/fixtures/file2.yaml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'stylish');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'plain');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.yaml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $diff = \Differ\Differ\genDiff($path1, $path2, 'json');
        $this->assertEquals($expected, $diff);
    }
}
