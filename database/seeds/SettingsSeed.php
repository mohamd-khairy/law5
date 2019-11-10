<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class SettingsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('Settings')->truncate();

        DB::table('Settings')->insert([
            'id' => '1',
            'automaticAssignDelay' => '60',
            'automaticIDAApproveDelay' => '60',
            'law5CertificatePercentage' => '40',
            'exportFundPercentage' => '10',

            'mailServer' => "smtp",
            'mailServerPort' => 25,
            'mailEnableSSL' => 0,
            'fromEmail' => "test@orchtech.com",
            'fromEmailPassword' => encrypt('TE@123456')
        ]);
    }
}
