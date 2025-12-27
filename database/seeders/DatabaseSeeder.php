<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    User,
    Admin,
    Country,
    City,
    Secretary,
    Physician,
    Patient,
    Appointment,
    Diagnosis
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // 1️⃣ Countries & Cities
        $this->seedLocations();

        // 2️⃣ Users & Roles
        $this->seedAdmin();
        $this->seedSecretaries();
        $this->seedPhysicians();
        $this->seedPatients();

        // 3️⃣ Appointments & Diagnoses
        $this->seedAppointments();

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📧 Demo Accounts:');
        $this->command->info('   Admin:     admin@example.com / password');
        $this->command->info('   Physician: alice@example.com / password');
        $this->command->info('   Secretary: secretary1@example.com / password');
        $this->command->info('   Patient:   jane@example.com / password');
    }

    /**
     * Seed countries and cities
     */
    private function seedLocations(): void
    {
        $this->command->info('📍 Seeding locations...');

        $usa = Country::create(['name' => 'United States']);
        $egypt = Country::create(['name' => 'Egypt']);
        $uk = Country::create(['name' => 'United Kingdom']);

        // USA Cities
        $usaCities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
        foreach ($usaCities as $cityName) {
            City::create(['name' => $cityName, 'country_id' => $usa->id]);
        }

        // Egypt Cities
        $egyptCities = ['Cairo', 'Alexandria', 'Giza', 'Luxor', 'Aswan'];
        foreach ($egyptCities as $cityName) {
            City::create(['name' => $cityName, 'country_id' => $egypt->id]);
        }

        // UK Cities
        $ukCities = ['London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow'];
        foreach ($ukCities as $cityName) {
            City::create(['name' => $cityName, 'country_id' => $uk->id]);
        }

        $this->command->info('✓ Locations seeded');
    }

    /**
     * Seed admin user
     */
    private function seedAdmin(): void
    {
        $this->command->info('👑 Seeding admin...');

        $adminUser = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'activation' => true,
        ]);

        Admin::create(['user_id' => $adminUser->id]);

        $this->command->info('✓ Admin created');
    }

    /**
     * Seed secretaries
     */
    private function seedSecretaries(): void
    {
        $this->command->info('📝 Seeding secretaries...');

        $secretaries = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'secretary1@example.com',
                'phone' => '555-0101',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'secretary2@example.com',
                'phone' => '555-0102',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'secretary3@example.com',
                'phone' => '555-0103',
            ],
        ];

        foreach ($secretaries as $sec) {
            $user = User::create([
                'name' => $sec['name'],
                'email' => $sec['email'],
                'password' => Hash::make('password'),
                'activation' => true,
            ]);

            Secretary::create([
                'user_id' => $user->id,
                'phone' => $sec['phone'],
                'city_id' => City::inRandomOrder()->first()->id,
            ]);
        }

        $this->command->info('✓ Secretaries created');
    }

    /**
     * Seed physicians
     */
    private function seedPhysicians(): void
    {
        $this->command->info('👨‍⚕️ Seeding physicians...');

        $physicians = [
            ['name' => 'Dr. Alice Williams', 'email' => 'alice@example.com', 'specialization' => 'Cardiology', 'phone' => '555-1001'],
            ['name' => 'Dr. Robert Smith', 'email' => 'robert@example.com', 'specialization' => 'Neurology', 'phone' => '555-1002'],
            ['name' => 'Dr. Jennifer Martinez', 'email' => 'jennifer@example.com', 'specialization' => 'Pediatrics', 'phone' => '555-1003'],
            ['name' => 'Dr. James Anderson', 'email' => 'james@example.com', 'specialization' => 'Orthopedics', 'phone' => '555-1004'],
            ['name' => 'Dr. Maria Garcia', 'email' => 'maria@example.com', 'specialization' => 'Dermatology', 'phone' => '555-1005'],
            ['name' => 'Dr. David Lee', 'email' => 'david@example.com', 'specialization' => 'Internal Medicine', 'phone' => '555-1006'],
            ['name' => 'Dr. Linda Taylor', 'email' => 'linda@example.com', 'specialization' => 'Psychiatry', 'phone' => '555-1007'],
            ['name' => 'Dr. Richard Wilson', 'email' => 'richard@example.com', 'specialization' => 'General Surgery', 'phone' => '555-1008'],
        ];

        foreach ($physicians as $doc) {
            $user = User::create([
                'name' => $doc['name'],
                'email' => $doc['email'],
                'password' => Hash::make('password'),
                'activation' => true,
            ]);

            Physician::create([
                'user_id' => $user->id,
                'specialization' => $doc['specialization'],
                'phone' => $doc['phone'],
                'city_id' => City::inRandomOrder()->first()->id,
            ]);
        }

        $this->command->info('✓ Physicians created');
    }

    /**
     * Seed patients
     */
    private function seedPatients(): void
    {
        $this->command->info('🏥 Seeding patients...');

        $patients = [
            ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '555-2001', 'national_id' => 'PAT001'],
            ['name' => 'John Smith', 'email' => 'john@example.com', 'phone' => '555-2002', 'national_id' => 'PAT002'],
            ['name' => 'Emma Wilson', 'email' => 'emma@example.com', 'phone' => '555-2003', 'national_id' => 'PAT003'],
            ['name' => 'Michael Johnson', 'email' => 'michael@example.com', 'phone' => '555-2004', 'national_id' => 'PAT004'],
            ['name' => 'Sophia Brown', 'email' => 'sophia@example.com', 'phone' => '555-2005', 'national_id' => 'PAT005'],
            ['name' => 'William Davis', 'email' => 'william@example.com', 'phone' => '555-2006', 'national_id' => 'PAT006'],
            ['name' => 'Olivia Martinez', 'email' => 'olivia@example.com', 'phone' => '555-2007', 'national_id' => 'PAT007'],
            ['name' => 'James Garcia', 'email' => 'jamesgarcia@example.com', 'phone' => '555-2008', 'national_id' => 'PAT008'],
            ['name' => 'Isabella Rodriguez', 'email' => 'isabella@example.com', 'phone' => '555-2009', 'national_id' => 'PAT009'],
            ['name' => 'Benjamin Lee', 'email' => 'benjamin@example.com', 'phone' => '555-2010', 'national_id' => 'PAT010'],
            ['name' => 'Mia Anderson', 'email' => 'mia@example.com', 'phone' => '555-2011', 'national_id' => 'PAT011'],
            ['name' => 'Lucas Taylor', 'email' => 'lucas@example.com', 'phone' => '555-2012', 'national_id' => 'PAT012'],
            ['name' => 'Charlotte Thomas', 'email' => 'charlotte@example.com', 'phone' => '555-2013', 'national_id' => 'PAT013'],
            ['name' => 'Henry Moore', 'email' => 'henry@example.com', 'phone' => '555-2014', 'national_id' => 'PAT014'],
            ['name' => 'Amelia Jackson', 'email' => 'amelia@example.com', 'phone' => '555-2015', 'national_id' => 'PAT015'],
        ];

        $secretaries = Secretary::all();

        foreach ($patients as $pat) {
            $user = User::create([
                'name' => $pat['name'],
                'email' => $pat['email'],
                'password' => Hash::make('password'),
                'activation' => true,
            ]);

            Patient::create([
                'user_id' => $user->id,
                'phone' => $pat['phone'],
                'national_id' => $pat['national_id'],
                'city_id' => City::inRandomOrder()->first()->id,
                'secretary_id' => $secretaries->random()->id,
            ]);
        }

        $this->command->info('✓ Patients created');
    }

    /**
     * Seed appointments and diagnoses
     */
    private function seedAppointments(): void
    {
        $this->command->info('📅 Seeding appointments...');

        $patients = Patient::all();
        $physicians = Physician::all();
        $secretaries = Secretary::all();

        $appointmentCount = 0;
        $diagnosisCount = 0;

        foreach ($patients as $patient) {
            // Each patient gets 2-4 appointments
            $numAppointments = rand(2, 4);

            for ($i = 0; $i < $numAppointments; $i++) {
                $physician = $physicians->random();
                $secretary = $secretaries->random();

                // Mix of past, today, and future appointments
                $daysOffset = match($i) {
                    0 => -rand(7, 30),    // Past appointment
                    1 => -rand(1, 6),     // Recent past
                    2 => 0,               // Today (some patients)
                    default => rand(1, 14) // Future
                };

                $appointmentDate = now()->addDays($daysOffset);
                $appointmentTime = sprintf('%02d:00', rand(9, 16)); // 9 AM to 4 PM

                // Determine status
                $status = match(true) {
                    $daysOffset < -1 => 'completed',
                    $daysOffset === 0 && rand(0, 1) === 0 => 'completed',
                    $daysOffset === 0 => 'scheduled',
                    $daysOffset > 0 => 'scheduled',
                    default => rand(0, 10) === 0 ? 'cancelled' : 'completed'
                };

                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'physician_id' => $physician->id,
                    'secretary_id' => $secretary->id,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'status' => $status,
                    'notes' => $this->getRandomAppointmentNote(),
                ]);

                $appointmentCount++;

                // Add diagnosis for completed appointments (80% chance)
                if ($status === 'completed' && rand(1, 10) <= 8) {
                    $diagnosisData = $this->getRandomDiagnosis();

                    Diagnosis::create([
                        'appointment_id' => $appointment->id,
                        'symptoms' => $diagnosisData['symptoms'],
                        'diagnosis' => $diagnosisData['diagnosis'],
                        'prescription' => $diagnosisData['prescription'],
                        'notes' => $diagnosisData['notes'],
                    ]);

                    $diagnosisCount++;
                }
            }
        }

        $this->command->info("✓ Created {$appointmentCount} appointments");
        $this->command->info("✓ Created {$diagnosisCount} diagnoses");
    }

    /**
     * Get random appointment notes
     */
    private function getRandomAppointmentNote(): string
    {
        $notes = [
            'Regular checkup appointment',
            'Follow-up visit for previous condition',
            'Patient requested urgent consultation',
            'Annual health screening',
            'Post-treatment evaluation',
            'New patient consultation',
            'Routine examination',
            'Patient experiencing symptoms',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Get random diagnosis data
     */
    private function getRandomDiagnosis(): array
    {
        $diagnoses = [
            [
                'symptoms' => 'Patient reports persistent headache, fatigue, and mild fever for 3 days. No recent travel history. Denies nausea or vision changes.',
                'diagnosis' => 'Upper respiratory tract infection (Common cold). Mild dehydration noted. No signs of bacterial infection.',
                'prescription' => "1. Paracetamol 500mg - Take 1 tablet every 6 hours as needed for fever\n2. Increase fluid intake to at least 2 liters per day\n3. Rest for 2-3 days\n4. Vitamin C 1000mg daily for 5 days",
                'notes' => 'Patient advised to return if fever persists beyond 3 days or if symptoms worsen. Avoid cold drinks and maintain proper rest.',
            ],
            [
                'symptoms' => 'Acute lower back pain for 2 days following heavy lifting. Pain radiates to right leg. No numbness or tingling. Difficulty bending forward.',
                'diagnosis' => 'Acute lumbar muscle strain. No signs of herniated disc or nerve compression. Normal range of motion in legs.',
                'prescription' => "1. Ibuprofen 400mg - Take 1 tablet three times daily with food for 5 days\n2. Apply hot compress to affected area 15-20 minutes, 3 times daily\n3. Bed rest for 48 hours\n4. Gentle stretching exercises after 2 days",
                'notes' => 'Patient educated on proper lifting techniques. Advised to avoid heavy lifting for 2 weeks. Return if pain worsens or numbness develops.',
            ],
            [
                'symptoms' => 'Dry cough for 1 week, mild chest tightness, no fever. Shortness of breath with exertion. History of seasonal allergies.',
                'diagnosis' => 'Allergic bronchitis likely triggered by seasonal allergens. Chest examination clear, no wheezing. Oxygen saturation 98%.',
                'prescription' => "1. Cetirizine 10mg - Once daily before bedtime for 7 days\n2. Dextromethorphan cough syrup - 10ml three times daily\n3. Steam inhalation twice daily\n4. Avoid known allergens and dusty environments",
                'notes' => 'Follow-up in 1 week if symptoms persist. Consider allergy testing if recurrent episodes occur. Maintain good hydration.',
            ],
            [
                'symptoms' => 'Severe sore throat, difficulty swallowing, fever 38.5°C. Enlarged tonsils with white patches. No cough or runny nose.',
                'diagnosis' => 'Acute bacterial tonsillitis (Streptococcal pharyngitis suspected). Positive throat examination findings. Lymph nodes enlarged.',
                'prescription' => "1. Amoxicillin 500mg - Take 1 capsule three times daily for 7 days (complete full course)\n2. Paracetamol 500mg for fever as needed\n3. Warm salt water gargles 4-5 times daily\n4. Throat lozenges as needed",
                'notes' => 'Emphasized importance of completing antibiotic course. Soft diet recommended. Return if breathing difficulty develops. Expected improvement in 48-72 hours.',
            ],
            [
                'symptoms' => 'Intermittent abdominal pain for 2 days, mainly upper abdomen. Bloating after meals. No vomiting or diarrhea. Stress at work mentioned.',
                'diagnosis' => 'Functional dyspepsia (Stress-induced gastritis). No alarming symptoms. Abdomen soft on examination.',
                'prescription' => "1. Omeprazole 20mg - Once daily before breakfast for 14 days\n2. Avoid spicy and fatty foods\n3. Eat small frequent meals\n4. Stress management techniques recommended",
                'notes' => 'Lifestyle modifications discussed. Return if symptoms worsen or if develops vomiting/blood in stool. Consider stress counseling.',
            ],
            [
                'symptoms' => 'Skin rash on arms and legs for 5 days. Itching moderate. No recent new medications or foods. No fever or joint pain.',
                'diagnosis' => 'Contact dermatitis (Allergic reaction suspected). Erythematous rash with mild scaling. No systemic involvement.',
                'prescription' => "1. Cetirizine 10mg - Once daily for 7 days\n2. Hydrocortisone cream 1% - Apply thin layer twice daily for 5 days\n3. Moisturizer after bathing\n4. Avoid scratching and use loose cotton clothing",
                'notes' => 'Advised to identify and avoid potential allergens. Keep skin moisturized. Return if spreading or worsening.',
            ],
            [
                'symptoms' => 'Generalized joint pain and stiffness, worse in mornings. Fatigue and occasional low-grade fever. Duration 10 days.',
                'diagnosis' => 'Viral arthralgia (Post-viral syndrome suspected). No joint swelling or redness. Symmetric involvement noted.',
                'prescription' => "1. Ibuprofen 400mg - Twice daily with food for 7 days\n2. Adequate rest and sleep\n3. Gentle exercises and stretching\n4. Multivitamin supplement",
                'notes' => 'Blood tests ordered (CBC, ESR, CRP) if symptoms persist beyond 2 weeks. Adequate hydration advised. Follow-up in 2 weeks.',
            ],
            [
                'symptoms' => 'Dizzy spells for 3 days, worse when standing quickly. No chest pain or palpitations. Reduced appetite and fluid intake.',
                'diagnosis' => 'Orthostatic hypotension secondary to dehydration. Blood pressure 95/60 mmHg. No cardiac abnormalities detected.',
                'prescription' => "1. Increase fluid intake to 2-3 liters daily\n2. Increase salt intake moderately\n3. Rise slowly from sitting/lying positions\n4. Small frequent meals",
                'notes' => 'Blood pressure monitoring at home recommended. Return if fainting occurs or if symptoms persist after hydration improvement.',
            ],
        ];

        return $diagnoses[array_rand($diagnoses)];
    }
}