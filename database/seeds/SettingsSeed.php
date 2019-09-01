<?php

use Illuminate\Database\Seeder;

class SettingsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('Settings')->truncate();

        DB::table('Settings')->insert([
            'id' => '1',
            'automaticAssignDelay' => '60',
            'automaticIDAApproveDelay' => '60',
            'law5CertificatePercentage' => '40',
            'exportFundPercentage' => '10',

            'mailServer' => null,
            'mailServerPort' => null,
            'mailEnableSSL' => null,
            'fromEmail' => null,
            'fromEmailPassword' => null
        ]);
    }
}
