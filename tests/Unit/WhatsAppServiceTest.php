<?php

namespace Tests\Unit;

use App\Services\WhatsAppService;
use PHPUnit\Framework\TestCase;

class WhatsAppServiceTest extends TestCase
{
    private WhatsAppService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new WhatsAppService();
    }

    public function test_peru_mobile_gets_country_code(): void
    {
        $this->assertSame(
            '51999123456',
            $this->service->normalizePhone('999123456')
        );
    }

    public function test_peru_mobile_with_plus_and_spaces_keeps_country_code(): void
    {
        $this->assertSame(
            '51999123456',
            $this->service->normalizePhone('+51 999 123 456')
        );
    }

    public function test_foreign_mobile_with_plus_is_kept(): void
    {
        $this->assertSame(
            '34612345678',
            $this->service->normalizePhone('+34 612 345 678')
        );
    }

    public function test_foreign_mobile_without_plus_is_kept(): void
    {
        $this->assertSame(
            '34612345678',
            $this->service->normalizePhone('34612345678')
        );
    }

    public function test_foreign_mobile_with_leading_00_is_normalized(): void
    {
        $this->assertSame(
            '34612345678',
            $this->service->normalizePhone('0034 612 345 678')
        );
    }

    public function test_invalid_number_throws_exception(): void
    {
        $this->expectException(\Exception::class);

        $this->service->normalizePhone('12345');
    }
}
