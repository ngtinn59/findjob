<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DesiredLevel; // Đảm bảo rằng model DesiredLevel đã tồn tại
use Illuminate\Foundation\Testing\RefreshDatabase;

class DesiredLevelsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test lấy danh sách desired levels.
     *
     * @return void
     */
    public function testIndexReturnsDesiredLevelsList()
    {
        // Tạo một vài desired levels
        DesiredLevel::factory()->count(3)->create();

        // Gửi yêu cầu GET đến API
        $response = $this->getJson('/api/admin/desired-levels');

        // Kiểm tra phản hồi
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'created_at', 'updated_at']
            ],
            'status_code'
        ]);
    }

    /**
     * Test tạo một desired level mới.
     *
     * @return void
     */
    public function testStoreCreatesNewDesiredLevel()
    {
        $data = [
            'name' => 'Advanced',
        ];

        // Gửi yêu cầu POST đến API
        $response = $this->postJson('/api/admin/desired-levels', $data);

        // Kiểm tra phản hồi
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Tạo desired level thành công!',
            'data' => ['name' => 'Advanced'],
            'status_code' => 200
        ]);

        // Kiểm tra trong cơ sở dữ liệu
        $this->assertDatabaseHas('desired_levels', $data);
    }

    /**
     * Test validation khi tạo một desired level với dữ liệu không hợp lệ.
     *
     * @return void
     */
    public function testStoreValidationFailsWithInvalidData()
    {
        $data = [
            'name' => 'A', // Dữ liệu không hợp lệ, vì name chỉ có 1 ký tự
        ];

        // Gửi yêu cầu POST đến API
        $response = $this->postJson('/api/admin/desired-levels', $data);

        // Kiểm tra phản hồi
        $response->assertStatus(400);
        $response->assertJsonValidationErrors('name');
    }

    /**
     * Test cập nhật thông tin desired level.
     *
     * @return void
     */
    public function testUpdateEditsDesiredLevel()
    {
        // Tạo một desired level trước
        $desiredLevel = DesiredLevel::factory()->create([
            'name' => 'Beginner',
        ]);

        // Dữ liệu cập nhật
        $data = [
            'name' => 'Intermediate',
        ];

        // Gửi yêu cầu PUT đến API
        $response = $this->putJson("/api/admin/desired-levels/{$desiredLevel->id}", $data);

        // Kiểm tra phản hồi
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Cập nhật desired level thành công',
            'data' => ['name' => 'Intermediate',],
            'status_code' => 200
        ]);

        // Kiểm tra trong cơ sở dữ liệu
        $this->assertDatabaseHas('desired_levels', $data);
    }

    /**
     * Test xóa một desired level.
     *
     * @return void
     */
    public function testDestroyDeletesDesiredLevel()
    {
        // Tạo một desired level trước
        $desiredLevel = DesiredLevel::factory()->create();

        // Gửi yêu cầu DELETE đến API
        $response = $this->deleteJson("/api/admin/desired-levels/{$desiredLevel->id}");

        // Kiểm tra phản hồi
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Xóa desired level thành công',
            'status_code' => 200
        ]);

        // Kiểm tra rằng desired level đã bị xóa khỏi cơ sở dữ liệu
        $this->assertDatabaseMissing('desired_levels', ['id' => $desiredLevel->id]);
    }
}
