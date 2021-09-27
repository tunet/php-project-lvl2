<?php

declare(strict_types=1);

namespace Tests\Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider getData
     */
    public function testGenDiff(string $filePath1, string $filePath2, string $formatter, string $expectedFilePath): void
    {
        $result = genDiff($filePath1, $filePath2, $formatter);
        $expected = file_get_contents($expectedFilePath);
        $this->assertSame($expected, $result);
    }

    public function testDefaultFormatter(): void
    {
        $result = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $expected = file_get_contents('tests/fixtures/result.stylish');
        $this->assertSame($expected, $result);
    }

    public function getData(): array
    {
        return [
            ['tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'stylish', 'tests/fixtures/result.stylish'],
            ['tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', 'stylish', 'tests/fixtures/result.stylish'],
            ['tests/fixtures/file1.json', 'tests/fixtures/file2.yaml', 'stylish', 'tests/fixtures/result.stylish'],
            ['tests/fixtures/file1.yml', 'tests/fixtures/file2.json', 'stylish', 'tests/fixtures/result.stylish'],
            ['tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'plain', 'tests/fixtures/result.plain'],
            ['tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'json', 'tests/fixtures/result.json'],
        ];
    }
}
