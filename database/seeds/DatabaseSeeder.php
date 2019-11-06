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
            // 'AssessmentMethodSeeder',
            // 'RoleSeeder',
            // 'CertificateTypeSeeder',
            // 'RequestStatusSeed',
            //'TestUsersFixtures',
            // 'SettingsSeed',
            // 'ActionSeed',
            // 'representative_type_seed'
            OldCertificateTableSeeder::class
        ]);
    }
}
