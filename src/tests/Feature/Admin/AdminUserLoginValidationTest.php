<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AdminUser;

class AdminUserLoginValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = AdminUser::factory()->create();
    }

    /**
     * メールアドレスが未入力の場合のバリデーションテスト
     */
    public function test_login_validation_without_email()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/admin/login')->assertSeeText('メールアドレスを入力してください');
    }

    /**
     * パスワードが未入力の場合のバリデーションテスト
     */
    public function test_login_validation_without_password()
    {
        $response = $this->post('/admin/login', [
            'email' => $this->adminUser->email,
            'password' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->get('/admin/login')->assertSeeText('パスワードを入力してください');
    }

    /**
     * 登録されていないメールアドレスでログインを試みた場合のテスト
     */
    public function test_login_with_unregistered_email()
    {
        $response = $this->post('/admin/login', [
            'email' => 'unregistered@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/admin/login')->assertSeeText('ログイン情報が登録されていません');
    }

    /**
     * パスワードが間違っている場合のテスト
     */
    public function test_login_with_wrong_password()
    {
        $response = $this->post('/admin/login', [
            'email' => $this->adminUser->email,
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->get('/admin/login')->assertSeeText('ログイン情報が登録されていません');
    }
}
