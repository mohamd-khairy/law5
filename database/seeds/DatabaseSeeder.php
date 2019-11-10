<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            'RoleSeeder',
            'TestUsersFixtures',
            'AssessmentMethodSeeder',
            'CertificateTypeSeeder',
            'RequestStatusSeed',
            'SettingsSeed',
            'ActionSeed',
            'representative_type_seed',
            OldCertificateTableSeeder::class
        ]);
    }
}
