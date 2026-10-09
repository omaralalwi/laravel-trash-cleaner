<?php

namespace Omaralalwi\LaravelTrashCleaner\Tests;

use Omaralalwi\LaravelTrashCleaner\LaravelTrashCleanerServiceProvider;

class ConfigPublishingTest extends TestCase
{
    private string $publishedConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishedConfig = config_path('laravel-trash-cleaner.php');
    }

    protected function tearDown(): void
    {
        @unlink($this->publishedConfig);

        parent::tearDown();
    }

    public function test_booting_the_provider_keeps_a_published_config_file(): void
    {
        file_put_contents($this->publishedConfig, "<?php\n\nreturn ['schedule' => true];\n");

        (new LaravelTrashCleanerServiceProvider($this->app))->boot();

        $this->assertFileExists($this->publishedConfig);
    }

    public function test_the_config_file_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'laravel-trash-cleaner'])->assertExitCode(0);

        $this->assertFileExists($this->publishedConfig);
    }
}
