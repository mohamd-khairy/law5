<?php

use Illuminate\Database\Seeder;

class representative_type_seed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('representative_types')->truncate();

        DB::table('representative_types')->insert(["id" => "1", 'nameEn' => 'concerned person', 'nameAr' => 'صاحب الشأن', 'needAttachment' => '0', 'key' => 'Concerned_person']);
        DB::table('representative_types')->insert(["id" => "2", 'nameEn' => 'representative', 'nameAr' => 'وكيل', 'needAttachment' => '1', 'key' => 'Representative']);
        DB::table('representative_types')->insert(["id" => "3", 'nameEn' => 'delegate', 'nameAr' => 'مفوض', 'needAttachment' => '1', 'key' => 'Delegate']);
    }
}
// needAttachment
