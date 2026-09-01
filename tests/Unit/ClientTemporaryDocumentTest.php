<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ClientTemporaryDocumentTest extends TestCase
{
    private function validateDocumentNumber(string $type, ?string $number)
    {
        return Validator::make(
            ['document_type' => $type, 'document_number' => $number],
            [
                'document_type' => ['required', 'string'],
                'document_number' => [
                    Rule::requiredIf(fn () => $type !== 'T'),
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]
        );
    }

    public function test_temporary_document_number_format_without_hyphens(): void
    {
        $when = \Carbon\Carbon::parse('2026-09-01 14:30:05');

        $this->assertSame('TMP20260901143005', Client::generateTemporaryDocumentNumber($when));
    }

    public function test_generated_temporary_document_number_has_prefix_and_no_hyphens(): void
    {
        $number = Client::generateTemporaryDocumentNumber();

        $this->assertStringStartsWith('TMP', $number);
        $this->assertSame(17, strlen($number));
        $this->assertStringNotContainsString('-', $number);
    }

    public function test_client_with_tmp_number_is_flagged_as_temporary(): void
    {
        $client = new Client(['document_number' => 'TMP20260901143005']);

        $this->assertTrue($client->is_temporary_document);
    }

    public function test_client_with_real_document_is_not_temporary(): void
    {
        $client = new Client(['document_number' => '12345678']);

        $this->assertFalse($client->is_temporary_document);
    }

    public function test_validation_passes_without_number_when_type_is_t(): void
    {
        $this->assertTrue($this->validateDocumentNumber('T', null)->passes());
    }

    public function test_validation_fails_without_number_when_type_is_dni(): void
    {
        $this->assertFalse($this->validateDocumentNumber('1', null)->passes());
    }

    public function test_validation_accepts_dni_number_when_type_is_dni(): void
    {
        $this->assertTrue($this->validateDocumentNumber('1', '12345678')->passes());
    }

    public function test_real_client_request_rules_pass_for_type_t_without_number(): void
    {
        $request = ClientRequest::create('/clients', 'POST', [
            'document_type' => 'T',
            'full_name' => 'JUAN PEREZ',
            'mobile' => '987654321',
        ]);

        $validator = app('validator')->make($request->all(), $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_real_client_request_rules_fail_for_dni_without_number(): void
    {
        $request = ClientRequest::create('/clients', 'POST', [
            'document_type' => '1',
            'full_name' => 'JUAN PEREZ',
            'mobile' => '987654321',
        ]);

        $validator = app('validator')->make($request->all(), $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('document_number', $validator->errors()->toArray());
    }
}
