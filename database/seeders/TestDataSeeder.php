<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\JobCard;
use App\Models\ServiceReport;
use App\Models\JobStatusUpdate;
use App\Models\PricingTemplate;
use App\Models\Part;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // =======================
        // ADMIN USER
        // =======================
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // =======================
        // MANAGER USERS
        // =======================
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'John Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('Manager123!'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager2@example.com'],
            [
                'name' => 'Sarah Operations Manager',
                'email' => 'manager2@example.com',
                'password' => Hash::make('Manager123!'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        // =======================
        // DATA CAPTURER USERS
        // =======================
        User::updateOrCreate(
            ['email' => 'capturer@example.com'],
            [
                'name' => 'Emma Data Capturer',
                'email' => 'capturer@example.com',
                'password' => Hash::make('Capturer123!'),
                'role' => 'data_capturer',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'capturer2@example.com'],
            [
                'name' => 'David Data Capturer',
                'email' => 'capturer2@example.com',
                'password' => Hash::make('Capturer123!'),
                'role' => 'data_capturer',
                'email_verified_at' => now(),
            ]
        );

        // =======================
        // COSTING OFFICER USERS
        // =======================
        User::updateOrCreate(
            ['email' => 'costing@example.com'],
            [
                'name' => 'Michael Costing Officer',
                'email' => 'costing@example.com',
                'password' => Hash::make('Costing123!'),
                'role' => 'costing_officer',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'costing2@example.com'],
            [
                'name' => 'Lisa Costing Officer',
                'email' => 'costing2@example.com',
                'password' => Hash::make('Costing123!'),
                'role' => 'costing_officer',
                'email_verified_at' => now(),
            ]
        );

        // =======================
        // TECHNICIAN USERS
        // =======================
        $technicianData = [
            [
                'email' => 'technician@example.com',
                'name' => 'Robert Technician',
                'specialization' => 'General Maintenance',
                'license_number' => 'LIC-001-2025',
                'skills' => 'Basic repairs, maintenance, inspections',
            ],
            [
                'email' => 'technician2@example.com',
                'name' => 'James Smith',
                'specialization' => 'Air Conditioning',
                'license_number' => 'LIC-002-2025',
                'skills' => 'AC repair, servicing, installation',
            ],
            [
                'email' => 'technician3@example.com',
                'name' => 'Peter Johnson',
                'specialization' => 'Electrical',
                'license_number' => 'LIC-003-2025',
                'skills' => 'Electrical repairs, wiring, diagnostics',
            ],
            [
                'email' => 'technician4@example.com',
                'name' => 'Vincent Brown',
                'specialization' => 'Plumbing',
                'license_number' => 'LIC-004-2025',
                'skills' => 'Plumbing repairs, installation, maintenance',
            ],
            [
                'email' => 'technician5@example.com',
                'name' => 'Andrew Davis',
                'specialization' => 'General Maintenance',
                'license_number' => 'LIC-005-2025',
                'skills' => 'General repairs, maintenance, troubleshooting',
            ],
        ];

        foreach ($technicianData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('Tech123!'),
                    'role' => 'technician',
                    'email_verified_at' => now(),
                ]
            );

            // Create technician profile with correct column names
            Technician::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $data['specialization'],
                    'license_number' => $data['license_number'],
                    'current_location_lat' => -17.8252 + ($faker->randomFloat(4, -0.5, 0.5)),
                    'current_location_lng' => 31.0335 + ($faker->randomFloat(4, -0.5, 0.5)),
                    'availability_status' => 'available',
                    'current_workload' => rand(0, 5),
                    'skills' => $data['skills'],
                ]
            );
        }

        // =======================
        // CUSTOMER USERS & PROFILES
        // =======================
        $customerCompanies = [
            [
                'company_name' => 'Harare Hospital',
                'contact_person' => 'Dr. Patricia Ndlovu',
                'email' => 'customer@hospital.com',
                'phone' => '+263 772 111 111',
                'city' => 'Harare',
            ],
            [
                'company_name' => 'Greenfield Manufacturing Ltd',
                'contact_person' => 'Mr. Tom Wilson',
                'email' => 'tom@greenfield.com',
                'phone' => '+263 773 222 222',
                'city' => 'Harare',
            ],
            [
                'company_name' => 'City Supermarket',
                'contact_person' => 'Ms. Grace Kumire',
                'email' => 'grace@citysupermarket.com',
                'phone' => '+263 774 333 333',
                'city' => 'Harare',
            ],
            [
                'company_name' => 'Tech Solutions Africa',
                'contact_person' => 'Mr. Charles Mubarak',
                'email' => 'charles@techsolutions.com',
                'phone' => '+263 775 444 444',
                'city' => 'Harare',
            ],
            [
                'company_name' => 'Cold Storage Logistics',
                'contact_person' => 'Ms. Jennifer Banda',
                'email' => 'jennifer@coldstorage.com',
                'phone' => '+263 776 555 555',
                'city' => 'Harare',
            ],
        ];

        foreach ($customerCompanies as $custData) {
            // First create the user account for customer
            $user = User::updateOrCreate(
                ['email' => $custData['email']],
                [
                    'name' => $custData['contact_person'],
                    'email' => $custData['email'],
                    'password' => Hash::make('Customer123!'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]
            );

            // Then create customer profile linked to user
            $customer = Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $custData['company_name'],
                    'contact_person' => $custData['contact_person'],
                    'phone' => $custData['phone'],
                    'email' => $custData['email'],
                    'address' => $faker->address,
                    'city' => $custData['city'],
                    'postal_code' => rand(10000, 99999),
                    'notes' => 'Customer registered for testing',
                ]
            );

            // Create machines for each customer with correct column names
            $machineTypes = ['Air Conditioner', 'Refrigerator', 'Generator', 'Pump', 'Compressor', 'Boiler'];
            $manufacturers = ['Siemens', 'GE', 'Danfoss', 'Copeland', 'Lennox', 'Trane'];
            
            for ($i = 1; $i <= rand(2, 4); $i++) {
                Machine::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'machine_name' => "Machine $i - {$custData['company_name']}",
                    ],
                    [
                        'model' => 'MODEL-' . strtoupper($faker->lexify('???')),
                        'serial_number' => 'SN-' . rand(100000, 999999),
                        'manufacturer' => $faker->randomElement($manufacturers),
                        'year_of_manufacture' => rand(2015, 2024),
                        'location' => 'Building ' . $faker->randomElement(['A', 'B', 'C', 'D']) . ', Floor ' . rand(1, 5),
                        'specifications' => json_encode([
                            'power' => rand(5, 50) . 'kW',
                            'capacity' => rand(10, 100) . 'L',
                            'rpm' => rand(800, 3600),
                        ]),
                        'last_service_date' => $faker->dateTimeThisYear(),
                    ]
                );
            }
        }

        // =======================
        // PRICING TEMPLATES
        // =======================
        $pricingData = [
            ['service_type' => 'breakdown', 'description' => 'Emergency Breakdown', 'labor_cost' => 150, 'minimum_charge' => 500],
            ['service_type' => 'maintenance', 'description' => 'Regular Maintenance', 'labor_cost' => 100, 'minimum_charge' => 300],
            ['service_type' => 'installation', 'description' => 'New Installation', 'labor_cost' => 200, 'minimum_charge' => 800],
        ];

        foreach ($pricingData as $pricing) {
            PricingTemplate::updateOrCreate(
                ['description' => $pricing['description']],
                [
                    'service_type' => $pricing['service_type'],
                    'labor_cost_per_hour' => $pricing['labor_cost'],
                    'minimum_charge' => $pricing['minimum_charge'],
                    'is_active' => true,
                ]
            );
        }

        // =======================
        // PARTS INVENTORY
        // =======================
        $partsData = [
            ['name' => 'Compressor Unit', 'number' => 'COMP-001', 'manufacturer' => 'Copeland', 'cost' => 1500],
            ['name' => 'Refrigerant Gas (R410A)', 'number' => 'REF-410A', 'manufacturer' => 'Chemours', 'cost' => 80],
            ['name' => 'Condenser Coil', 'number' => 'COND-001', 'manufacturer' => 'Lennox', 'cost' => 800],
            ['name' => 'Expansion Valve', 'number' => 'EXP-001', 'manufacturer' => 'Danfoss', 'cost' => 250],
            ['name' => 'Air Filter', 'number' => 'FILT-001', 'manufacturer' => 'Generic', 'cost' => 50],
            ['name' => 'Capacitor', 'number' => 'CAP-001', 'manufacturer' => 'Generic', 'cost' => 100],
            ['name' => 'Thermostat', 'number' => 'THERM-001', 'manufacturer' => 'Honeywell', 'cost' => 150],
            ['name' => 'Fan Motor', 'number' => 'MOTOR-001', 'manufacturer' => 'GE', 'cost' => 450],
        ];

        foreach ($partsData as $part) {
            Part::updateOrCreate(
                ['part_number' => $part['number']],
                [
                    'part_name' => $part['name'],
                    'manufacturer' => $part['manufacturer'],
                    'cost' => $part['cost'],
                    'quantity_in_stock' => rand(5, 50),
                    'description' => "High-quality {$part['name']} for HVAC systems",
                ]
            );
        }

        // =======================
        // SERVICE REQUESTS
        // =======================
        $customers = Customer::all();
        $requestTypes = ['breakdown', 'maintenance', 'installation'];
        $statuses = ['submitted', 'assessed', 'assigned', 'in_progress', 'completed'];

        foreach ($customers as $customer) {
            $machineCount = rand(2, 3);
            for ($i = 0; $i < $machineCount; $i++) {
                $machine = $customer->machines->random();
                $referenceNumber = 'SR-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

                ServiceRequest::updateOrCreate(
                    ['reference_number' => $referenceNumber],
                    [
                        'customer_id' => $customer->id,
                        'machine_id' => $machine->id,
                        'request_type' => $faker->randomElement($requestTypes),
                        'request_description' => $faker->sentence,
                        'requires_assessment' => $faker->boolean,
                        'status' => $faker->randomElement($statuses),
                        'submitted_at' => $faker->dateTimeThisMonth(),
                        'completed_at' => $faker->randomElement([null, now()]),
                    ]
                );
            }
        }

        // =======================
        // JOB CARDS
        // =======================
        $serviceRequests = ServiceRequest::whereIn('status', ['assigned', 'in_progress', 'completed'])->get();
        $technicians = Technician::all();

        foreach ($serviceRequests->take(10) as $request) {
            $technician = $technicians->random();
            $jobReference = 'JC-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

            $jobCard = JobCard::updateOrCreate(
                ['service_request_id' => $request->id],
                [
                    'technician_id' => $technician->id,
                    'job_reference' => $jobReference,
                    'status' => $faker->randomElement(['pending', 'in_progress', 'completed']),
                    'estimated_duration' => rand(1, 8),
                    'started_at' => $faker->randomElement([null, now()->subHours(rand(1, 48))]),
                    'completed_at' => $faker->randomElement([null, now()]),
                    'notes' => $faker->sentence,
                ]
            );

            // Create job status updates for in-progress jobs
            if ($jobCard->status === 'in_progress' || $jobCard->status === 'completed') {
                JobStatusUpdate::updateOrCreate(
                    ['job_card_id' => $jobCard->id],
                    [
                        'status' => $jobCard->status,
                        'location_lat' => -17.8252 + ($faker->randomFloat(4, -0.5, 0.5)),
                        'location_lng' => 31.0335 + ($faker->randomFloat(4, -0.5, 0.5)),
                        'notes' => 'Status updated: ' . ucfirst($jobCard->status),
                    ]
                );
            }

            // Create service report for completed jobs
            if ($jobCard->status === 'completed') {
                ServiceReport::updateOrCreate(
                    ['job_card_id' => $jobCard->id],
                    [
                        'technician_id' => $technician->id,
                        'work_completed' => 'All maintenance tasks completed successfully. Equipment tested and working normally.',
                        'parts_used' => json_encode([
                            'Air Filter' => ['quantity' => 2, 'cost' => 50],
                            'Capacitor' => ['quantity' => 1, 'cost' => 100],
                        ]),
                        'labor_hours' => rand(2, 8),
                        'additional_notes' => 'Equipment is in good condition. Recommend next service in 6 months.',
                    ]
                );
            }
        }

        $this->command->info('✅ Test data seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - ' . User::count() . ' Users created');
        $this->command->info('   - ' . Technician::count() . ' Technicians created');
        $this->command->info('   - ' . Customer::count() . ' Customers created');
        $this->command->info('   - ' . Machine::count() . ' Machines created');
        $this->command->info('   - ' . ServiceRequest::count() . ' Service Requests created');
        $this->command->info('   - ' . JobCard::count() . ' Job Cards created');
        $this->command->info('   - ' . ServiceReport::count() . ' Service Reports created');
        $this->command->info('   - ' . PricingTemplate::count() . ' Pricing Templates created');
        $this->command->info('   - ' . Part::count() . ' Parts created');
        $this->command->info('');
        $this->command->info('🔐 Ready to login! Use credentials from QUICK_REFERENCE.md');
    }
}