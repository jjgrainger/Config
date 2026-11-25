<?php

use Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_it_creates_with_empty_config(): void
    {
        $config = new Config;

        $this->assertEquals([], $config->all());
    }

    public function test_it_creates_with_passed_config(): void
    {
        $items = [
            'item' => 'value',
            'key' => 123,
            'nested' => [
                'item' => true,
            ]
        ];

        $config = new Config($items);

        $this->assertSame($items, $config->all());
    }

    public function test_it_gets_value_from_config(): void
    {
        $items = [
            'item' => 'value',
            'key' => 123,
        ];

        $config = new Config($items);

        $this->assertEquals('value', $config->get('item'));
        $this->assertEquals(123, $config->get('key'));
    }

    public function test_it_gets_nested_value_from_config(): void
    {
        $items = [
            'level1' => [
                'level2' => [
                    'level3' => 'value',
                    'key' => false,
                ]
            ]
        ];

        $config = new Config($items);

        $this->assertEquals('value', $config->get('level1.level2.level3'));
        $this->assertEquals(false, $config->get('level1.level2.key'));
        $this->assertEquals(['level3' => 'value', 'key' => false], $config->get('level1.level2'));
    }

    public function test_it_gets_returns_default_if_value_does_not_exist(): void
    {
        $items = [
            'item' => 'value',
            'key' => 123,
        ];

        $config = new Config($items);

        $this->assertEquals(null, $config->get('invalid'));
        $this->assertEquals(123, $config->get('config', 123));
    }

    public function test_it_returns_boolean_for_has_value(): void
    {
        $items = [
            'item' => 'value',
            'key' => 123,
            'nested' => [
                'item' => false,
                'level2' => [
                    'item' => null,
                ],
            ],
        ];

        $config = new Config($items);

        $this->assertFalse($config->has('invalid'));
        $this->assertTrue($config->has('key'));
        $this->assertTrue($config->has('nested'));
        $this->assertTrue($config->has('nested.item'));
        $this->assertTrue($config->has('nested.level2.item'));
        $this->assertFalse($config->has('nested.level2.invalid'));
    }
}
