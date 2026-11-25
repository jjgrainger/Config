<?php

use Config\Exceptions\InvalidConfigurationException;
use Config\Parsers\PhpParser;
use PHPUnit\Framework\TestCase;

class PhpParserTest extends TestCase
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

    public function test_it_only_supports_php(): void
    {
        $parser = new PhpParser;

        $this->assertTrue($parser->supports('php'));
        $this->assertFalse($parser->supports('json'));
        $this->assertFalse($parser->supports('yaml'));
        $this->assertFalse($parser->supports('ini'));
    }

    public function test_parse_php_array_file(): void
    {
        file_put_contents(
            $this->directory . '/config.php',
            '<?php return ["app" => "My Application"];'
        );

        $parser = new PhpParser;
        $data = $parser->parse($this->directory . '/config.php');

        $this->assertEquals(['app' => 'My Application'], $data);
    }

    public function test_parse_empty_array(): void
    {
        file_put_contents(
            $this->directory . '/config.php',
            '<?php return [];'
        );

        $parser = new PhpParser;
        $data = $parser->parse($this->directory . '/config.php');

        $this->assertEquals([], $data);
    }

    public function test_parse_php_throws_exception_for_file_not_returning_array(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageIs('Configuration file does not return an array: ' . $this->directory . '/config.php');

        file_put_contents(
            $this->directory . '/config.php',
            '<?php return "My Application";'
        );

        $parser = new PhpParser;
        $parser->parse($this->directory . '/config.php');
    }

    public function test_config_file_exception_is_thrown_not_caught(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageIs('something went wrong');

        file_put_contents(
            $this->directory . '/config.php',
            '<?php throw new Exception("something went wrong");'
        );

        $parser = new PhpParser;
        $parser->parse($this->directory . '/config.php');
    }
}
