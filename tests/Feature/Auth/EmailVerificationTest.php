<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AccountVerification;
use Illuminate\Support\Facades\Hash;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->email = 'qwe@qwe';
        $this->password = 'qweqweqweQ1';

        $this->user = User::factory()->create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => null,
        ]);
        
        $this->data = ['email' => $this->email];

        $this->code = Str::random(AccountVerification::EMAIL_HASH_LENGTH);

        $this->verification = AccountVerification::factory()->create([
            'code'    => Hash::make( $this->code ),
            'type'    => AccountVerification::EMAIL_TYPE,
            'user_id' => $this->user->id,
        ]);

        // Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);
        
        $this->apiCreateOrUpdateEndpoint = '/api/email-verification/create-or-update';
        $this->webAttemptEndpoint = "/api/email-verification/uid/{$this->user->id}/c/{$this->code}";
    }

    public function test_success()
    {
        $response = $this->get($this->webAttemptEndpoint, $this->data);

        $response->assertOk();
    }

    public function test_already_verified()
    {
        $this->user->email_verified_at = now();
        $this->user->save();
        $this->user->fresh();

        $response = $this->get($this->webAttemptEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_already_verified_deletes_verification()
    {
        $this->user->email_verified_at = now();
        $this->user->save();
        $this->user->fresh();

        $this->get($this->webAttemptEndpoint, $this->data);
        
        $this->assertDatabaseMissing('account_verifications', [
            'type' => AccountVerification::EMAIL_TYPE,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_incorrect_code()
    {
        $code = $this->code . 'sasdasd';
        $Endpoint = "/api/email-verification/uid/{$this->user->id}/c/{$code}";

        $response = $this->get($Endpoint, $this->data);

        $response->assertJson([
            'status' => 'error',
            'code' => 404
        ]);
    }
}
