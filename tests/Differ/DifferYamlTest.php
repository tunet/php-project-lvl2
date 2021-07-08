<?php

declare(strict_types=1);

namespace Differ;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferYamlTest extends TestCase
{
    public function testGenDiff(): void
    {
        $result = genDiff('tests/fixtures/yaml/file1.yml', 'tests/fixtures/yaml/file2.yaml');
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
