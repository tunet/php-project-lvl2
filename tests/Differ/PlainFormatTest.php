<?php

declare(strict_types=1);

namespace Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class PlainFormatTest extends TestCase
{
    public function testGenDiff(): void
    {
        $result = genDiff(__DIR__ . '/../fixtures/json/file1.json', __DIR__ . '/../fixtures/json/file2.json', 'plain');
        $expectedResult = <<<EXPECTED
        Property 'common.follow' was added with value: false
        Property 'common.setting2' was removed
        Property 'common.setting3' was updated. From true to null
        Property 'common.setting4' was added with value: 'blah blah'
        Property 'common.setting5' was added with value: [complex value]
        Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
        Property 'common.setting6.ops' was added with value: 'vops'
        Property 'group1.baz' was updated. From 'bas' to 'bars'
        Property 'group1.nest' was updated. From [complex value] to 'str'
        Property 'group2' was removed
        Property 'group3' was added with value: [complex value]
        EXPECTED;

        $this->assertSame($expectedResult, $result);
    }
}
