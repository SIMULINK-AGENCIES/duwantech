<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActiveSession;
use App\Models\User;
use Carbon\Carbon;

class GeographicLocationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Sample location data for different countries
        $locations = [
            [
                'user_name' => 'John Doe',
                'country' => 'United States',
                'country_code' => 'US',
                'city' => 'New York',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'timezone' => 'America/New_York',
                'user_id' => 1
            ],
            [
                'user_name' => 'Jane Smith',
                'country' => 'United Kingdom',
                'country_code' => 'GB',
                'city' => 'London',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
                'timezone' => 'Europe/London',
                'user_id' => 2
            ],
            [
                'user_name' => 'Anonymous User',
                'country' => 'Germany',
                'country_code' => 'DE',
                'city' => 'Berlin',
                'latitude' => 52.5200,
                'longitude' => 13.4050,
                'timezone' => 'Europe/Berlin',
                'user_id' => null
            ],
            [
                'user_name' => 'Maria Garcia',
                'country' => 'Spain',
                'country_code' => 'ES',
                'city' => 'Madrid',
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'timezone' => 'Europe/Madrid',
                'user_id' => 3
            ],
            [
                'user_name' => 'Anonymous Visitor',
                'country' => 'France',
                'country_code' => 'FR',
                'city' => 'Paris',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'timezone' => 'Europe/Paris',
                'user_id' => null
            ],
            [
                'user_name' => 'Hiroshi Tanaka',
                'country' => 'Japan',
                'country_code' => 'JP',
                'city' => 'Tokyo',
                'latitude' => 35.6762,
                'longitude' => 139.6503,
                'timezone' => 'Asia/Tokyo',
                'user_id' => 1
            ],
            [
                'user_name' => 'Sarah Johnson',
                'country' => 'Australia',
                'country_code' => 'AU',
                'city' => 'Sydney',
                'latitude' => -33.8688,
                'longitude' => 151.2093,
                'timezone' => 'Australia/Sydney',
                'user_id' => 2
            ],
            [
                'user_name' => 'Guest User',
                'country' => 'Canada',
                'country_code' => 'CA',
                'city' => 'Toronto',
                'latitude' => 43.6532,
                'longitude' => -79.3832,
                'timezone' => 'America/Toronto',
                'user_id' => null
            ],
            [
                'user_name' => 'Peter Müller',
                'country' => 'Switzerland',
                'country_code' => 'CH',
                'city' => 'Zurich',
                'latitude' => 47.3769,
                'longitude' => 8.5417,
                'timezone' => 'Europe/Zurich',
                'user_id' => 3
            ],
            [
                'user_name' => 'Anna Kowalski',
                'country' => 'Poland',
                'country_code' => 'PL',
                'city' => 'Warsaw',
                'latitude' => 52.2297,
                'longitude' => 21.0122,
                'timezone' => 'Europe/Warsaw',
                'user_id' => 1
            ],
            [
                'user_name' => 'David Kim',
                'country' => 'South Korea',
                'country_code' => 'KR',
                'city' => 'Seoul',
                'latitude' => 37.5665,
                'longitude' => 126.9780,
                'timezone' => 'Asia/Seoul',
                'user_id' => 2
            ],
            [
                'user_name' => 'Anonymous Browser',
                'country' => 'Brazil',
                'country_code' => 'BR',
                'city' => 'São Paulo',
                'latitude' => -23.5505,
                'longitude' => -46.6333,
                'timezone' => 'America/Sao_Paulo',
                'user_id' => null
            ]
        ];

        // Create active sessions with location data
        foreach ($locations as $index => $location) {
            $sessionId = 'sample_session_' . ($index + 1) . '_' . time();
            $ipAddress = '192.168.' . rand(1, 255) . '.' . rand(1, 255);
            
            $locationData = [
                'country' => $location['country'],
                'country_code' => $location['country_code'],
                'region' => $location['city'] . ' Region',
                'city' => $location['city'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'timezone' => $location['timezone'],
                'isp' => 'Sample ISP',
                'accuracy' => 'city',
                'updated_at' => now()
            ];

            ActiveSession::create([
                'user_id' => $location['user_id'],
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => 'Mozilla/5.0 (compatible; Sample Bot)',
                'location_data' => json_encode($locationData),
                'page_url' => 'http://localhost:8000/',
                'last_activity' => Carbon::now()->subMinutes(rand(1, 10)),
                'created_at' => Carbon::now()->subMinutes(rand(5, 30)),
                'updated_at' => Carbon::now()->subMinutes(rand(1, 10))
            ]);
        }

        $this->command->info('Created ' . count($locations) . ' sample geographic location sessions');
    }
}
