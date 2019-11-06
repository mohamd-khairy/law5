<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('roles')->truncate();

        DB::table('roles')->insert(['nameEn' => 'Admin', 'nameAr' => 'مدير', 'key' => 'Admin']);
        DB::table('roles')->insert(['nameEn' => 'Applicant', 'nameAr' => 'مقدم الطلب', 'key' => 'Applicant']);
        DB::table('roles')->insert(['nameEn' => 'IDA Manager', 'nameAr' => 'مدير IDA', 'key' => 'IDAManager']);
        DB::table('roles')->insert(['nameEn' => 'IDA Employee', 'nameAr' => ' موظف IDA', 'key' => 'IDAEmployee']);
        DB::table('roles')->insert(['nameEn' => 'FEI Employee', 'nameAr' => 'موظف FEI', 'key' => 'FEIEmployee']);
        DB::table('roles')->insert(['nameEn' => 'Chamber Employee', 'nameAr' => 'موظف غرفة', 'key' => 'ChamberEmployee']);
        DB::table('roles')->insert(['nameEn' => 'EOS User', 'nameAr' => 'مستخدم EOS', 'key' => 'EOSUser']);
        DB::table('roles')->insert(['nameEn' => 'GAGS User', 'nameAr' => 'مستخدم GAGS', 'key' => 'GAGSUser']);
    }
}
