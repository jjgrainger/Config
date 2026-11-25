<?php

use Config\Exceptions\InvalidSourceException;
use Config\Source;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
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

    public function test_it_returns_files_for_file_location(): void
    {
        file_put_contents(
            $this->directory . '/app.php',
            '<?php return ["name" => "App"];'
        );

        $source = new Source($this->directory . '/app.php');

        $files = [
            [
                'path' => $this->directory . '/app.php',
                'name' => 'app',
                'extension' => 'php',
            ]
        ];

        $this->assertSame($files, $source->files());
    }

    public function test_it_returns_files_for_directory_location(): void
    {
        file_put_contents(
            $this->directory . '/app.php',
            '<?php return ["name" => "App"];'
        );

        file_put_contents(
            $this->directory . '/database.php',
            '<?php return ["database" => "App", "port" => 3306];'
        );

        $source = new Source($this->directory);

        $files = [
            [
                'path' => $this->directory . '/app.php',
                'name' => 'app',
                'extension' => 'php',
            ],
            [
                'path' => $this->directory . '/database.php',
                'name' => 'database',
                'extension' => 'php',
            ],
        ];

        $this->assertSame($files, $source->files());
    }

    public function test_it_throws_exception_when_file_does_not_exist(): void
    {
        $this->expectException(InvalidSourceException::class);
        $this->expectExceptionMessageIsOrContains("Location does not exist:");

        $source = new Source($this->directory . '/app.php');
    }

    public function test_it_throws_exception_when_directory_does_not_exist(): void
    {
        $this->expectException(InvalidSourceException::class);
        $this->expectExceptionMessageIsOrContains("Location does not exist:");

        $source = new Source($this->directory . '/missing');
    }
}
