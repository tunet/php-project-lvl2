<?php

declare(strict_types=1);

namespace Differ;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $result = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $expectedResult = <<<EXPECTED
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        EXPECTED;

        $this->assertSame($expectedResult, $result);
    }
}
