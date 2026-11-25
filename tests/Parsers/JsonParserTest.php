<?php

use Config\Exceptions\InvalidConfigurationException;
use Config\Parsers\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    protected string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/config-' . uniqid();

        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*') as $file) {
            unlink($file);
        }

        rmdir($this->directory);
    }

    public function test_it_only_supports_json(): void
    {
        $parser = new JsonParser;

        $this->assertTrue($parser->supports('json'));
        $this->assertFalse($parser->supports('php'));
        $this->assertFalse($parser->supports('yaml'));
        $this->assertFalse($parser->supports('ini'));
    }

    public function test_it_parses_json_file(): void
    {
        $data = ['app' => 'My Application'];

        file_put_contents(
            $this->directory . '/config.json',
            json_encode($data)
        );

        $parser = new JsonParser;
        $config = $parser->parse($this->directory . '/config.json');

        $this->assertEquals($data, $config);
    }

    public function test_it_parses_empty_array(): void
    {
        file_put_contents(
            $this->directory . '/config.json',
            json_encode([])
        );

        $parser = new JsonParser;
        $config = $parser->parse($this->directory . '/config.json');

        $this->assertEquals([], $config);
    }

    public function test_it_throws_exception_for_file_not_returning_array(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageIs('Configuration file does not return an array: ' . $this->directory . '/config.json');

        file_put_contents(
            $this->directory . '/config.json',
            json_encode("My Application")
        );

        $parser = new JsonParser;
        $parser->parse($this->directory . '/config.json');
    }

    public function test_it_throws_json_exception(): void
    {
        $this->expectException(JsonException::class);

        file_put_contents(
            $this->directory . '/config.json',
            '{invalid json}'
        );

        $parser = new JsonParser;
        $parser->parse($this->directory . '/config.json');
    }
}
