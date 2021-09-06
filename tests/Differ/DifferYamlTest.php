<?php

declare(strict_types=1);

namespace Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferYamlTest extends TestCase
{
    public function testGenDiff(): void
    {
        $result = genDiff(__DIR__ . '/../fixtures/yaml/file1.yml', __DIR__ . '/../fixtures/yaml/file2.yaml');
        $expectedResult = <<<EXPECTED
        {
            common: {
              + follow: false
                setting1: Value 1
              - setting2: 200
              - setting3: true
              + setting3: null
              + setting4: blah blah
              + setting5: {
                    key5: value5
                }
                setting6: {
                    doge: {
                      - wow: 
                      + wow: so much
                    }
                    key: value
                  + ops: vops
                }
            }
            group1: {
              - baz: bas
              + baz: bars
                foo: bar
              - nest: {
                    key: value
                }
              + nest: str
            }
          - group2: {
                abc: 12345
                deep: {
                    id: 45
                }
            }
          + group3: {
                deep: {
                    id: {
                        number: 45
                    }
                }
                fee: 100500
            }
        }
        EXPECTED;

        $this->assertSame($expectedResult, $result);
    }
}
