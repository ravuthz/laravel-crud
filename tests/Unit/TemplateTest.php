<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ravuthz\LaravelCrud\Crud\Template;

class TemplateTest extends TestCase
{
    private string $stubPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stubPath = tempnam(sys_get_temp_dir(), 'crud_stub_');
        file_put_contents($this->stubPath, 'Hello {{ name }}');
    }

    protected function tearDown(): void
    {
        @unlink($this->stubPath);

        parent::tearDown();
    }

    public function test_generate_replaces_placeholders(): void
    {
        $template = Template::generate($this->stubPath, [
            '{{ name }}' => 'Laravel',
        ]);

        $this->assertSame('Hello Laravel', $template);
    }

    public function test_generate_returns_stub_content_without_replacements(): void
    {
        $this->assertSame('Hello {{ name }}', Template::generate($this->stubPath));
    }
}
