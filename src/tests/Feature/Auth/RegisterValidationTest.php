<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    /** 名前が未入力の場合のバリデーションテスト */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password-confirmation' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('name');
        $this->get('/register')->assertSeeText('お名前を入力してください');
    }

    /** メールアドレスが未入力の場合のバリデーションテスト */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password-confirmation' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/register')->assertSeeText('メールアドレスを入力してください');
    }

    /** メールアドレスの形式が不正な場合のバリデーションテスト */
    public function test_email_must_be_valid_format()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password-confirmation' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/register')->assertSeeText('メールアドレスを入力してください');
    }

    /** パスワードが未入力の場合のバリデーションテスト */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password-confirmation' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->get('/register')->assertSeeText('パスワードを入力してください');
    }

    /** パスワードが短すぎる場合のバリデーションテスト */
    public function test_password_must_be_min_length()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '123',
            'password-confirmation' => '123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->get('/register')->assertSeeText('パスワードは8文字以上で入力してください');
    }

    /** パスワード確認が一致しない場合のバリデーションテスト */
    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password-confirmation' => 'different-password'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password-confirmation');
        $this->get('/register')->assertSeeText('パスワードと一致しません');
    }

    /** ユーザー登録が正常に完了することを確認するテスト */
    public function test_user_can_register_successfully()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password-confirmation' => 'password123'
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(302);
        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);

        $this->assertAuthenticated();
    }
}
