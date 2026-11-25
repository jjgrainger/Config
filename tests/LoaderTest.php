<?php

use Config\Contracts\ParserContract;
use Config\Contracts\SourceContract;
use Config\Exceptions\UnsupportedExtensionException;
use Config\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    public function test_it_returns_config(): void
    {
        $source = $this->createMock(SourceContract::class);
        $source->expects($this->once())
            ->method('files')
            ->willReturn([
                [
                    'name' => 'app',
                    'path' => '/app.php',
                    'extension' => 'php',
                ]
            ]);

        $parser = $this->createMock(ParserContract::class);
        $parser->expects($this->once())
            ->method('supports')
            ->with('php')
            ->willReturn(true);

        $parser->expects($this->once())
            ->method('parse')
            ->with('/app.php')
            ->willReturn(['name' => 'App']);


        $loader = new Loader([$parser]);
        $config = $loader->load($source);

        $this->assertSame(
            [
                'app' => [
                    'name' => 'App'
                ]
            ],
            $config
        );
    }

    public function test_it_returns_config_for_multiple_file_types(): void
    {
        $source = $this->createMock(SourceContract::class);
        $source->expects($this->once())
            ->method('files')
            ->willReturn([
                [
                    'name' => 'app',
                    'path' => '/app.php',
                    'extension' => 'php',
                ],
                [
                    'name' => 'database',
                    'path' => '/database.json',
                    'extension' => 'json',
                ]
            ]);

        $php = $this->createMock(ParserContract::class);
        $php->expects($this->exactly(2))
            ->method('supports')
            ->willReturnMap([
                ['php', true],
                ['json', false],
            ]);

        $php->expects($this->once())
            ->method('parse')
            ->with('/app.php')
            ->willReturn(['name' => 'App']);

        $json = $this->createMock(ParserContract::class);
        $json->expects($this->once())
            ->method('supports')
            ->with('json')
            ->willReturn(true);

        $json->expects($this->once())
            ->method('parse')
            ->with('/database.json')
            ->willReturn(['database' => 'app_db']);


        $loader = new Loader([$php, $json]);
        $config = $loader->load($source);

        $this->assertSame(
            [
                'app' => [
                    'name' => 'App'
                ],
                'database' => [
                    'database' => 'app_db',
                ]
            ],
            $config
        );
    }

    public function test_it_throws_exception_for_unsupported_extension(): void
    {
        $this->expectException(UnsupportedExtensionException::class);
        $this->expectExceptionMessageIs("Unsupported file extension: php");

        $source = $this->createMock(SourceContract::class);
        $source->expects($this->once())
            ->method('files')
            ->willReturn([
                [
                    'name' => 'app',
                    'path' => '/app.php',
                    'extension' => 'php',
                ]
            ]);

        $parser = $this->createMock(ParserContract::class);
        $parser->expects($this->once())
            ->method('supports')
            ->with('php')
            ->willReturn(false);

        $parser->expects($this->never())
            ->method('parse');


        $loader = new Loader([$parser]);
        $loader->load($source);
    }
}
