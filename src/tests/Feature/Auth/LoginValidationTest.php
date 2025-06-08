<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * メールアドレスが未入力の場合のバリデーションテスト
     */
    public function test_login_validation_without_email()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/login')->assertSeeText('メールアドレスを入力してください');
    }

    /**
     * パスワードが未入力の場合のバリデーションテスト
     */
    public function test_login_validation_without_password()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->get('/login')->assertSeeText('パスワードを入力してください');
    }

    /**
     * 登録されていないメールアドレスでログインを試みた場合のテスト
     */
    public function test_login_with_unregistered_email()
    {
        $response = $this->post('/login', [
            'email' => 'unregistered@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/login')->assertSeeText('ログイン情報が登録されていません');
    }

    /**
     * パスワードが間違っている場合のテスト
     */
    public function test_login_with_wrong_password()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/login')->assertSeeText('ログイン情報が登録されていません');
    }
}
