<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Parser;
use Gendiff\Formatters;


class genDiffTest extends TestCase
{


    public function testgenDiff(): void
    {
        
        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'stylish');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'plain');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'json');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.yml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'stylish');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.yml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'plain');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.yml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'json');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yaml');
        $path2 = (__DIR__ . '/fixtures/file2.yaml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestStylishFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'stylish');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.yml');
        $path2 = (__DIR__ . '/fixtures/file2.json');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'plain');
        $this->assertEquals($expected, $diff);

        $path1 = (__DIR__ . '/fixtures/file1.json');
        $path2 = (__DIR__ . '/fixtures/file2.yaml');
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $diff = Gendiff\Diff\genDiff($path1, $path2, 'json');
        $this->assertEquals($expected, $diff);
    }


    
}
