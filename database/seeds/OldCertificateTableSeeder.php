<?php

use Illuminate\Database\Seeder;

class OldCertificateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Model\OldCertificate::class, 50)->create();
    }
}
