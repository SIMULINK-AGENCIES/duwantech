<?php

namespace Tests;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DebugLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Create admin user
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $user->assignRole($adminRole);
        
        return $user;
    }

    public function test_debug_admin_route_access()
    {
        $user = $this->createAdminUser();
        
        // Test the basic /admin route
        $response = $this->actingAs($user)->get('/admin');
        
        // Log the response for debugging
        echo "\nResponse Status: " . $response->getStatusCode();
        echo "\nResponse Content: " . $response->getContent();
        
        $response->assertStatus(200);
    }

    public function test_debug_dashboard_route_access()
    {
        $user = $this->createAdminUser();
        
        // Test the dashboard route directly - it's actually /admin not /admin/dashboard
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        // Log the response for debugging
        echo "\nDashboard Response Status: " . $response->getStatusCode();
        if ($response->getStatusCode() !== 200) {
            echo "\nDashboard Response Content: " . substr($response->getContent(), 0, 1000);
        } else {
            echo "\nDashboard Response Content Length: " . strlen($response->getContent());
        }
        
        $response->assertStatus(200);
    }
}
