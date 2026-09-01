<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ClientPhoneValidationTest extends TestCase
{
    private function passes(string $mobile): bool
    {
        return Validator::make(
            ['mobile' => $mobile],
            ['mobile' => ['required', 'string', 'regex:'.config('options.mobile_regex')]]
        )->passes();
    }

    public function test_peru_mobile_passes(): void
    {
        $this->assertTrue($this->passes('999123456'));
    }

    public function test_foreign_mobile_with_plus_passes(): void
    {
        $this->assertTrue($this->passes('+34612345678'));
    }

    public function test_foreign_mobile_without_plus_passes(): void
    {
        $this->assertTrue($this->passes('34612345678'));
    }

    public function test_peru_mobile_not_starting_with_9_fails(): void
    {
        $this->assertFalse($this->passes('112345678'));
    }

    public function test_too_short_number_fails(): void
    {
        $this->assertFalse($this->passes('12345'));
    }
}
