<?php

declare(strict_types=1);

namespace Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class JsonFormatTest extends TestCase
{
    public function testGenDiff(): void
    {
        $result = genDiff(__DIR__ . '/../fixtures/json/file1.json', __DIR__ . '/../fixtures/json/file2.json', 'json');
        $expectedResult = <<<EXPECTED
        [
            {
                "operation": "not_changed",
                "type": "object",
                "key": "common",
                "value": [
                    {
                        "operation": "added",
                        "type": "simple",
                        "key": "follow",
                        "value": false
                    },
                    {
                        "operation": "not_changed",
                        "type": "simple",
                        "key": "setting1",
                        "value": "Value 1"
                    },
                    {
                        "operation": "removed",
                        "type": "simple",
                        "key": "setting2",
                        "value": 200
                    },
                    {
                        "operation": "changed",
                        "oldType": "simple",
                        "newType": "simple",
                        "key": "setting3",
                        "oldValue": true,
                        "newValue": null
                    },
                    {
                        "operation": "added",
                        "type": "simple",
                        "key": "setting4",
                        "value": "blah blah"
                    },
                    {
                        "operation": "added",
                        "type": "object",
                        "key": "setting5",
                        "value": [
                            {
                                "operation": "not_changed",
                                "type": "simple",
                                "key": "key5",
                                "value": "value5"
                            }
                        ]
                    },
                    {
                        "operation": "not_changed",
                        "type": "object",
                        "key": "setting6",
                        "value": [
                            {
                                "operation": "not_changed",
                                "type": "object",
                                "key": "doge",
                                "value": [
                                    {
                                        "operation": "changed",
                                        "oldType": "simple",
                                        "newType": "simple",
                                        "key": "wow",
                                        "oldValue": "",
                                        "newValue": "so much"
                                    }
                                ]
                            },
                            {
                                "operation": "not_changed",
                                "type": "simple",
                                "key": "key",
                                "value": "value"
                            },
                            {
                                "operation": "added",
                                "type": "simple",
                                "key": "ops",
                                "value": "vops"
                            }
                        ]
                    }
                ]
            },
            {
                "operation": "not_changed",
                "type": "object",
                "key": "group1",
                "value": [
                    {
                        "operation": "changed",
                        "oldType": "simple",
                        "newType": "simple",
                        "key": "baz",
                        "oldValue": "bas",
                        "newValue": "bars"
                    },
                    {
                        "operation": "not_changed",
                        "type": "simple",
                        "key": "foo",
                        "value": "bar"
                    },
                    {
                        "operation": "changed",
                        "oldType": "object",
                        "newType": "simple",
                        "key": "nest",
                        "oldValue": [
                            {
                                "operation": "not_changed",
                                "type": "simple",
                                "key": "key",
                                "value": "value"
                            }
                        ],
                        "newValue": "str"
                    }
                ]
            },
            {
                "operation": "removed",
                "type": "object",
                "key": "group2",
                "value": [
                    {
                        "operation": "not_changed",
                        "type": "simple",
                        "key": "abc",
                        "value": 12345
                    },
                    {
                        "operation": "not_changed",
                        "type": "object",
                        "key": "deep",
                        "value": [
                            {
                                "operation": "not_changed",
                                "type": "simple",
                                "key": "id",
                                "value": 45
                            }
                        ]
                    }
                ]
            },
            {
                "operation": "added",
                "type": "object",
                "key": "group3",
                "value": [
                    {
                        "operation": "not_changed",
                        "type": "object",
                        "key": "deep",
                        "value": [
                            {
                                "operation": "not_changed",
                                "type": "object",
                                "key": "id",
                                "value": [
                                    {
                                        "operation": "not_changed",
                                        "type": "simple",
                                        "key": "number",
                                        "value": 45
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "operation": "not_changed",
                        "type": "simple",
                        "key": "fee",
                        "value": 100500
                    }
                ]
            }
        ]
        EXPECTED;

        $this->assertSame($expectedResult, $result);
    }
}
