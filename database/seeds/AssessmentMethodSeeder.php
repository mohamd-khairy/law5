<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class AssessmentMethodSeeder
 * @todo rename this class to "InitialLookupEntitiesSeeder"
 */
class AssessmentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $idDetailed = DB::table('assessment_methods')->insertGetId(['nameEn' => 'detailed', 'nameAr' => 'مفصل']);
        $idTotals   = DB::table('assessment_methods')->insertGetId(['nameEn' => 'totals', 'nameAr' => 'اجماليات']);
       
        DB::table('chambers')->truncate();

        DB::table('chambers')->insert(['nameEn' => 'Chamber of Engineering Industries', 'nameAr' => 'غرفة الصناعات الهندسية', 'assessmentMethod' => $idDetailed]);
        DB::table('chambers')->insert(['nameEn' => 'Chamber of Handicrafts', 'nameAr' => 'غرفة الحرف اليدوية', 'assessmentMethod' => $idTotals]);
       
        DB::table('sectors')->truncate();
        DB::table('sectors')->insert(['nameEn' => 'Food Sector', 'nameAr' => 'الإدارة الغذائية']);
        DB::table('sectors')->insert(['nameEn' => 'Engineering Sector', 'nameAr' => 'الإدارة الهندسية']);
        DB::table('sectors')->insert(['nameEn' => 'Handicrafts Sector', 'nameAr' => 'إدارة الصناعات الصغيرة']);
    }
}
