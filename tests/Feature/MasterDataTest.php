<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MetalType;
use App\Models\Project;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    // Client Tests
    public function test_admin_can_view_clients_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertSee('Clients');
    }

    public function test_admin_can_create_client()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('clients.store'), [
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', ['name' => 'Test Client']);
    }

    public function test_client_soft_deletes_work()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        $this->actingAs($admin)->delete(route('clients.destroy', $client));

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    // Vehicle Tests
    public function test_admin_can_create_vehicle()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('vehicles.store'), [
            'registration_number' => 'ABC123',
            'type' => 'Truck',
            'model' => 'Test Model',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('vehicles.index'));
        $this->assertDatabaseHas('vehicles', ['registration_number' => 'ABC123']);
    }

    public function test_vehicle_registration_must_be_unique()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Vehicle::create(['registration_number' => 'ABC123', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('vehicles.store'), [
            'registration_number' => 'ABC123',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('registration_number');
    }

    // Metal Type Tests
    public function test_admin_can_create_metal_type()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('metal-types.store'), [
            'name' => 'Steel',
            'description' => 'High quality steel',
            'unit_price' => 100.50,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('metal-types.index'));
        $this->assertDatabaseHas('metal_types', ['name' => 'Steel']);
    }

    public function test_metal_type_name_must_be_unique()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        MetalType::create(['name' => 'Steel', 'unit_price' => 100, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('metal-types.store'), [
            'name' => 'Steel',
            'unit_price' => 150,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('name');
    }

    // Project Tests
    public function test_admin_can_create_project()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'description' => 'Test Description',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }

    public function test_project_belongs_to_client()
    {
        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);
        $project = Project::create([
            'name' => 'Test Project',
            'client_id' => $client->id,
            'status' => 'pending',
        ]);

        $this->assertEquals($client->id, $project->client->id);
    }

    // Access Control Tests
    public function test_non_admin_cannot_access_master_data()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('vehicles.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('metal-types.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('projects.index'));
        $response->assertStatus(403);
    }

    public function test_sidebar_shows_master_data_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('Master Data');
        $response->assertSee(route('clients.index'));
    }
}
