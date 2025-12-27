<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Realistic Clients
        $clients = [
            [
                'name' => 'Sunrise Construction Ltd',
                'email' => 'contact@sunriseconst.com',
                'phone' => '9876543210',
                'address' => '123 Industrial Area, Hyderabad',
            ],
            [
                'name' => 'Metro Infra Projects',
                'email' => 'info@metroinfra.in',
                'phone' => '9876543211',
                'address' => '45 Jubilee Hills, Hyderabad',
            ],
            [
                'name' => 'Green Valley Developers',
                'email' => 'projects@greenvalley.com',
                'phone' => '9876543212',
                'address' => '78 Gachibowli, Hyderabad',
            ],
            [
                'name' => 'Skyline Builders',
                'email' => 'sales@skyline.com',
                'phone' => '9876543213',
                'address' => '12 Hitech City, Hyderabad',
            ],
            [
                'name' => 'Urban Spaces Pvt Ltd',
                'email' => 'contact@urbanspaces.in',
                'phone' => '9876543214',
                'address' => '90 Banjara Hills, Hyderabad',
            ],
            [
                'name' => 'Reliable Roads Corp',
                'email' => 'contracts@reliableroads.com',
                'phone' => '9876543215',
                'address' => '56 Begumpet, Hyderabad',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::firstOrCreate(['email' => $clientData['email']], $clientData);
        }

        // 2. Create Realistic Vehicles
        $vehicles = [
            ['registration_number' => 'TS07GK1234', 'type' => 'Tipper', 'model' => 'Tata LPK 2518', 'is_active' => true],
            ['registration_number' => 'TS08HJ5678', 'type' => 'Tipper', 'model' => 'Ashok Leyland 2518', 'is_active' => true],
            ['registration_number' => 'TS09KL9012', 'type' => 'Excavator', 'model' => 'JCB 3DX', 'is_active' => true],
            ['registration_number' => 'TS10MN3456', 'type' => 'Excavator', 'model' => 'Hitachi EX200', 'is_active' => false], // Under maintenance
            ['registration_number' => 'TS11OP7890', 'type' => 'Loader', 'model' => 'JCB 4DX', 'is_active' => true],
            ['registration_number' => 'TS12QR1234', 'type' => 'Tipper', 'model' => 'BharatBenz 2823C', 'is_active' => true],
            ['registration_number' => 'TS13ST5678', 'type' => 'Crusher', 'model' => 'Metso Nordberg', 'is_active' => true],
            ['registration_number' => 'TS14UV9012', 'type' => 'Tipper', 'model' => 'Tata Prima', 'is_active' => false],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::firstOrCreate(['registration_number' => $vehicleData['registration_number']], $vehicleData);
        }

        // 3. Create Projects linked to Clients
        $dbClients = Client::all();

        if ($dbClients->count() > 0) {
            $projects = [
                ['name' => 'Highway Expansion NH-65', 'status' => 'active', 'progress' => 45],
                ['name' => 'Metro Line Extension Phase 2', 'status' => 'active', 'progress' => 70],
                ['name' => 'Silicon Valley Tech Park', 'status' => 'pending', 'progress' => 0],
                ['name' => 'Greenfield Airport Road', 'status' => 'completed', 'progress' => 100],
                ['name' => 'Riverside Apartment Complex', 'status' => 'active', 'progress' => 25],
                ['name' => 'Central Mall Excavation', 'status' => 'completed', 'progress' => 100],
                ['name' => 'Industrial Park Zone B', 'status' => 'pending', 'progress' => 0],
                ['name' => 'Flyover Construction @ Hub', 'status' => 'active', 'progress' => 60],
                ['name' => 'School Building Block C', 'status' => 'cancelled', 'progress' => 10],
                ['name' => 'Hospital Wing Extension', 'status' => 'active', 'progress' => 85],
            ];

            foreach ($projects as $index => $projectData) {
                Project::create([
                    'name' => $projectData['name'],
                    'client_id' => $dbClients->random()->id,
                    'location' => 'Hyderabad, TS',
                    'description' => 'Detailed construction project regarding ' . $projectData['name'],
                    'estimated_quantity' => rand(1000, 50000),
                    'start_date' => Carbon::now()->subDays(rand(10, 200)),
                    'end_date' => Carbon::now()->addDays(rand(30, 300)),
                    'status' => $projectData['status'],
                    'progress' => $projectData['progress'],
                ]);
            }
        }
    }
}
