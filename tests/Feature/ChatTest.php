<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles if they don't exist
        if (!Role::where('name', 'sales')->exists()) {
            Role::create(['name' => 'sales']);
        }
        if (!Role::where('name', 'reservation')->exists()) {
            Role::create(['name' => 'reservation']);
        }
    }

    public function test_sales_user_can_view_chat_messages()
    {
        $user = User::factory()->create();
        $user->assignRole('sales');
        
        $inquiry = Inquiry::factory()->create();
        $chat = Chat::factory()->create(['inquiry_id' => $inquiry->id]);

        $response = $this->actingAs($user)
            ->getJson("/dashboard/inquiries/{$inquiry->id}/chats");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'inquiry_id',
                        'sender_id',
                        'message',
                        'read_at',
                        'created_at',
                        'updated_at',
                        'sender' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ]
                ]
            ]);
    }

    public function test_sales_user_can_send_chat_message()
    {
        $user = User::factory()->create();
        $user->assignRole('sales');
        
        $inquiry = Inquiry::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/dashboard/inquiries/{$inquiry->id}/chats", [
                'message' => 'Test message'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'inquiry_id',
                    'sender_id',
                    'message',
                    'read_at',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('chats', [
            'inquiry_id' => $inquiry->id,
            'sender_id' => $user->id,
            'message' => 'Test message'
        ]);
    }

    public function test_unauthorized_user_cannot_access_chat()
    {
        $user = User::factory()->create();
        $inquiry = Inquiry::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/dashboard/inquiries/{$inquiry->id}/chats");

        $response->assertStatus(403);
    }

    public function test_chat_message_validation()
    {
        $user = User::factory()->create();
        $user->assignRole('sales');
        
        $inquiry = Inquiry::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/dashboard/inquiries/{$inquiry->id}/chats", [
                'message' => ''
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }
}
