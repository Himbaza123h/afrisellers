<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Vendor\Vendor;
use App\Models\Buyer\Buyer;
use App\Models\BusinessProfile;
use App\Models\OwnerID;
use App\Models\Country;
use App\Models\Plan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@afrisellers.com'],
            [
                'name' => 'Afrisellers Admin',
                'password' => Hash::make('Admin@2025'),
                'email_verified_at' => now(),
            ],
        );

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        // Get default plan or first plan (to be used for all vendors)
        $defaultPlan = Plan::where('is_default', true)->first() ?? Plan::orderBy('id', 'asc')->first();
        
        if (!$defaultPlan) {
            throw new \Exception('No plan found. Please run PlanSeeder first.');
        }

        // 2. Create Verified Vendor User - Kenya
        $vendorUser1 = User::firstOrCreate(
            ['email' => 'vendor1@afrisellers.com'],
            [
                'name' => 'John Kamau',
                'password' => Hash::make('Vendor@2025'),
                'email_verified_at' => now(),
            ],
        );

        $vendorRole = Role::where('slug', 'vendor')->first();
        if ($vendorRole && !$vendorUser1->roles()->where('role_id', $vendorRole->id)->exists()) {
            $vendorUser1->roles()->attach($vendorRole->id);
        }

        $kenya = Country::where('name', 'Kenya')->first();
        $businessProfile1 = BusinessProfile::firstOrCreate(
            ['business_registration_number' => 'KE-2025-001'],
            [
                'user_id' => $vendorUser1->id,
                'country_id' => $kenya->id ?? 1,
                'business_name' => 'Kamau Trading Co.',
                'phone' => '712345678',
                'phone_code' => '+254',
                'city' => 'Nairobi',
                'verification_status' => 'verified',
                'is_admin_verified' => true,
            ],
        );

        $ownerID1 = OwnerID::firstOrCreate(
            ['user_id' => $vendorUser1->id],
            [
                'id_number' => '12345678',
                'id_document_path' => 'vendor/documents/sample-id.pdf',
                'business_document_path' => 'vendor/documents/sample-business.pdf',
            ],
        );

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser1->id],
            [
                'business_profile_id' => $businessProfile1->id,
                'owner_id_document_id' => $ownerID1->id,
                'plan_id' => $defaultPlan?->id,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
                'account_status' => 'active',
                'email_verified' => true,
            ],
        );

        // 3. Create Second Verified Vendor User - Tanzania
        $vendorUser2 = User::firstOrCreate(
            ['email' => 'vendor2@afrisellers.com'],
            [
                'name' => 'Amina Hassan',
                'password' => Hash::make('Vendor@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($vendorRole && !$vendorUser2->roles()->where('role_id', $vendorRole->id)->exists()) {
            $vendorUser2->roles()->attach($vendorRole->id);
        }

        $tanzania = Country::where('name', 'Tanzania')->first();
        $businessProfile2 = BusinessProfile::firstOrCreate(
            ['business_registration_number' => 'TZ-2025-002'],
            [
                'user_id' => $vendorUser2->id,
                'country_id' => $tanzania->id ?? 1,
                'business_name' => 'Hassan Electronics Ltd',
                'phone' => '789456123',
                'phone_code' => '+255',
                'city' => 'Dar es Salaam',
                'verification_status' => 'verified',
                'is_admin_verified' => true,
            ],
        );

        $ownerID2 = OwnerID::firstOrCreate(
            ['user_id' => $vendorUser2->id],
            [
                'id_number' => '87654321',
                'id_document_path' => 'vendor/documents/sample-id.pdf',
                'business_document_path' => 'vendor/documents/sample-business.pdf',
            ],
        );

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser2->id],
            [
                'business_profile_id' => $businessProfile2->id,
                'owner_id_document_id' => $ownerID2->id,
                'plan_id' => $defaultPlan?->id,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
                'account_status' => 'active',
                'email_verified' => true,
            ],
        );

        // 4. Create Pending Vendor User - Ghana
        $vendorUser3 = User::firstOrCreate(
            ['email' => 'vendor3@afrisellers.com'],
            [
                'name' => 'Kwame Mensah',
                'password' => Hash::make('Vendor@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($vendorRole && !$vendorUser3->roles()->where('role_id', $vendorRole->id)->exists()) {
            $vendorUser3->roles()->attach($vendorRole->id);
        }

        $ghana = Country::where('name', 'Ghana')->first();
        $businessProfile3 = BusinessProfile::firstOrCreate(
            ['business_registration_number' => 'GH-2025-003'],
            [
                'user_id' => $vendorUser3->id,
                'country_id' => $ghana->id ?? 1,
                'business_name' => 'Mensah Agro Supplies',
                'phone' => '244567890',
                'phone_code' => '+233',
                'city' => 'Accra',
                'verification_status' => 'pending',
                'is_admin_verified' => false,
            ],
        );

        $ownerID3 = OwnerID::firstOrCreate(
            ['user_id' => $vendorUser3->id],
            [
                'id_number' => '11223344',
                'id_document_path' => 'vendor/documents/sample-id.pdf',
                'business_document_path' => 'vendor/documents/sample-business.pdf',
            ],
        );

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser3->id],
            [
                'business_profile_id' => $businessProfile3->id,
                'owner_id_document_id' => $ownerID3->id,
                'plan_id' => $defaultPlan?->id,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => null,
                'account_status' => 'pending',
                'email_verified' => false,
            ],
        );

        // 5. Create Unverified Email Vendor User - South Africa
        $vendorUser4 = User::firstOrCreate(
            ['email' => 'vendor4@afrisellers.com'],
            [
                'name' => 'Fatima Nkosi',
                'password' => Hash::make('Vendor@2025'),
                'email_verified_at' => null,
            ],
        );

        if ($vendorRole && !$vendorUser4->roles()->where('role_id', $vendorRole->id)->exists()) {
            $vendorUser4->roles()->attach($vendorRole->id);
        }

        $southAfrica = Country::where('name', 'South Africa')->first();
        $businessProfile4 = BusinessProfile::firstOrCreate(
            ['business_registration_number' => 'ZA-2025-004'],
            [
                'user_id' => $vendorUser4->id,
                'country_id' => $southAfrica->id ?? 1,
                'business_name' => 'Nkosi Textiles',
                'phone' => '821234567',
                'phone_code' => '+27',
                'city' => 'Johannesburg',
                'verification_status' => 'pending',
                'is_admin_verified' => false,
            ],
        );

        $ownerID4 = OwnerID::firstOrCreate(
            ['user_id' => $vendorUser4->id],
            [
                'id_number' => '55667788',
                'id_document_path' => 'vendor/documents/sample-id.pdf',
                'business_document_path' => 'vendor/documents/sample-business.pdf',
            ],
        );

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser4->id],
            [
                'business_profile_id' => $businessProfile4->id,
                'owner_id_document_id' => $ownerID4->id,
                'plan_id' => $defaultPlan?->id,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => null,
                'account_status' => 'pending',
                'email_verified' => false,
            ],
        );

        // 6. Create Suspended Vendor User - Senegal
        $vendorUser5 = User::firstOrCreate(
            ['email' => 'vendor5@afrisellers.com'],
            [
                'name' => 'Ibrahim Diallo',
                'password' => Hash::make('Vendor@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($vendorRole && !$vendorUser5->roles()->where('role_id', $vendorRole->id)->exists()) {
            $vendorUser5->roles()->attach($vendorRole->id);
        }

        $senegal = Country::where('name', 'Senegal')->first();
        $businessProfile5 = BusinessProfile::firstOrCreate(
            ['business_registration_number' => 'SN-2025-005'],
            [
                'user_id' => $vendorUser5->id,
                'country_id' => $senegal->id ?? 1,
                'business_name' => 'Diallo Construction Supplies',
                'phone' => '771234567',
                'phone_code' => '+221',
                'city' => 'Dakar',
                'verification_status' => 'verified',
                'is_admin_verified' => true,
            ],
        );

        $ownerID5 = OwnerID::firstOrCreate(
            ['user_id' => $vendorUser5->id],
            [
                'id_number' => '99887766',
                'id_document_path' => 'vendor/documents/sample-id.pdf',
                'business_document_path' => 'vendor/documents/sample-business.pdf',
            ],
        );

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser5->id],
            [
                'business_profile_id' => $businessProfile5->id,
                'owner_id_document_id' => $ownerID5->id,
                'plan_id' => $defaultPlan?->id,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
                'account_status' => 'suspended',
                'email_verified' => true,
            ],
        );

        // 7. Create Verified Vendor User - Rwanda
        $vendorUser6 = User::firstOrCreate(
            ['email' => 'vendor6@afrisellers.com'],
            [
                'name' => 'Claude Mugisha',
                'password' => Hash::make('Vendor@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($vendorRole && !$vendorUser6->roles()->where('role_id', $vendorRole->id)->exists()) {
            $vendorUser6->roles()->attach($vendorRole->id);
        }

        $rwanda = Country::where('name', 'Rwanda')->first();
        $businessProfile6 = BusinessProfile::firstOrCreate(
            ['business_registration_number' => 'RW-2025-006'],
            [
                'user_id' => $vendorUser6->id,
                'country_id' => $rwanda->id ?? 1,
                'business_name' => 'Mugisha Coffee Exports',
                'phone' => '788123456',
                'phone_code' => '+250',
                'city' => 'Kigali',
                'verification_status' => 'verified',
                'is_admin_verified' => true,
            ],
        );

        $ownerID6 = OwnerID::firstOrCreate(
            ['user_id' => $vendorUser6->id],
            [
                'id_number' => '44332211',
                'id_document_path' => 'vendor/documents/sample-id.pdf',
                'business_document_path' => 'vendor/documents/sample-business.pdf',
            ],
        );

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser6->id],
            [
                'business_profile_id' => $businessProfile6->id,
                'owner_id_document_id' => $ownerID6->id,
                'plan_id' => $defaultPlan?->id,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
                'account_status' => 'active',
                'email_verified' => true,
            ],
        );

        // 8. Create Buyer User 1 (Female, Active, Verified) - Nigeria
        $buyerUser1 = User::firstOrCreate(
            ['email' => 'buyer1@afrisellers.com'],
            [
                'name' => 'Sarah Okonkwo',
                'password' => Hash::make('Buyer@2025'),
                'email_verified_at' => now(),
            ],
        );

        $buyerRole = Role::where('slug', 'buyer')->first();
        if ($buyerRole && !$buyerUser1->roles()->where('role_id', $buyerRole->id)->exists()) {
            $buyerUser1->roles()->attach($buyerRole->id);
        }

        $nigeria = Country::where('name', 'Nigeria')->first();
        Buyer::firstOrCreate(
            ['user_id' => $buyerUser1->id],
            [
                'phone' => '802345678',
                'phone_code' => '+234',
                'country_id' => $nigeria->id ?? 1,
                'city' => 'Lagos',
                'date_of_birth' => '1988-05-15',
                'sex' => 'Female',
                'account_status' => 'active',
                'email_verified' => true,
                'email_verified_at' => now(),
            ],
        );

        // 9. Create Buyer User 2 (Male, Active, Verified) - DR Congo
        $buyerUser2 = User::firstOrCreate(
            ['email' => 'buyer2@afrisellers.com'],
            [
                'name' => 'Patrick Mutombo',
                'password' => Hash::make('Buyer@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($buyerRole && !$buyerUser2->roles()->where('role_id', $buyerRole->id)->exists()) {
            $buyerUser2->roles()->attach($buyerRole->id);
        }

        $congo = Country::where('name', 'Congo')->first();
        Buyer::firstOrCreate(
            ['user_id' => $buyerUser2->id],
            [
                'phone' => '998765432',
                'phone_code' => '+243',
                'country_id' => $congo->id ?? 1,
                'city' => 'Kinshasa',
                'date_of_birth' => '1992-08-22',
                'sex' => 'Male',
                'account_status' => 'active',
                'email_verified' => true,
                'email_verified_at' => now(),
            ],
        );

        // 10. Create Buyer User 3 (Female, Pending Email Verification) - Kenya
        $buyerUser3 = User::firstOrCreate(
            ['email' => 'buyer3@afrisellers.com'],
            [
                'name' => 'Zainab Ahmed',
                'password' => Hash::make('Buyer@2025'),
                'email_verified_at' => null,
            ],
        );

        if ($buyerRole && !$buyerUser3->roles()->where('role_id', $buyerRole->id)->exists()) {
            $buyerUser3->roles()->attach($buyerRole->id);
        }

        Buyer::firstOrCreate(
            ['user_id' => $buyerUser3->id],
            [
                'phone' => '712345678',
                'phone_code' => '+254',
                'country_id' => $kenya->id ?? 1,
                'city' => 'Mombasa',
                'date_of_birth' => '1995-03-10',
                'sex' => 'Female',
                'account_status' => 'pending',
                'email_verified' => false,
                'email_verified_at' => null,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            ],
        );

        // 11. Create Buyer User 4 (Male, Suspended) - Ghana
        $buyerUser4 = User::firstOrCreate(
            ['email' => 'buyer4@afrisellers.com'],
            [
                'name' => 'David Nkrumah',
                'password' => Hash::make('Buyer@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($buyerRole && !$buyerUser4->roles()->where('role_id', $buyerRole->id)->exists()) {
            $buyerUser4->roles()->attach($buyerRole->id);
        }

        Buyer::firstOrCreate(
            ['user_id' => $buyerUser4->id],
            [
                'phone' => '244567890',
                'phone_code' => '+233',
                'country_id' => $ghana->id ?? 1,
                'city' => 'Kumasi',
                'date_of_birth' => '1985-12-05',
                'sex' => 'Male',
                'account_status' => 'suspended',
                'email_verified' => true,
                'email_verified_at' => now(),
            ],
        );

        // 12. Create Buyer User 5 (Female, Active, Verified) - Rwanda
        $buyerUser5 = User::firstOrCreate(
            ['email' => 'buyer5@afrisellers.com'],
            [
                'name' => 'Ange Uwase',
                'password' => Hash::make('Buyer@2025'),
                'email_verified_at' => now(),
            ],
        );

        if ($buyerRole && !$buyerUser5->roles()->where('role_id', $buyerRole->id)->exists()) {
            $buyerUser5->roles()->attach($buyerRole->id);
        }

        Buyer::firstOrCreate(
            ['user_id' => $buyerUser5->id],
            [
                'phone' => '788654321',
                'phone_code' => '+250',
                'country_id' => $rwanda->id ?? 1,
                'city' => 'Kigali',
                'date_of_birth' => '1990-07-18',
                'sex' => 'Female',
                'account_status' => 'active',
                'email_verified' => true,
                'email_verified_at' => now(),
            ],
        );

        // 13. Create Regular Customer User (no specific role) - Rwanda
        $customer1 = User::firstOrCreate(
            ['email' => 'customer1@afrisellers.com'],
            [
                'name' => 'Grace Uwimana',
                'password' => Hash::make('Customer@2025'),
                'email_verified_at' => now(),
            ],
        );

        // 14. Create Another Regular Customer User - Kenya
        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@afrisellers.com'],
            [
                'name' => 'Moses Kipchoge',
                'password' => Hash::make('Customer@2025'),
                'email_verified_at' => now(),
            ],
        );

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('📧 Admin: admin@afrisellers.com | Password: Admin@2025');
        $this->command->info('📧 Vendors: vendor1-6@afrisellers.com | Password: Vendor@2025');
        $this->command->info('📧 Buyers: buyer1-5@afrisellers.com | Password: Buyer@2025');
        $this->command->info('📧 Customers: customer1-2@afrisellers.com | Password: Customer@2025');
    }
}
