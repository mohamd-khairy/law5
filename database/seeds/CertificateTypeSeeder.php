<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('certificate_types')->truncate();

        DB::table('certificate_types')->insert(['name' => 'Export Fund' , 'nameAr' => 'شهادة المساندة التصديرية']);
        DB::table('certificate_types')->insert(['name' => 'Law5', 'nameAr' => '5 شهادة قانون رقم ']);
    }
}
