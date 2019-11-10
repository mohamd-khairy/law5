<?php

use Illuminate\Database\Seeder;

class RequestStatusSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('requeststatus')->truncate();

        DB::table('requeststatus')->insert(['id' => '1','nameAr' =>'جديد','nameEn' => 'New','key' => 'New']);
        DB::table('requeststatus')->insert(['id' => '2','nameAr' =>'معين لموظف','nameEn' => 'Assigned','key' => 'Assigned']);
        DB::table('requeststatus')->insert(['id' => '3','nameAr' =>'تم مراجعته','nameEn' => 'UnderReview Closed','key' => 'UnderReview_Closed']);
        DB::table('requeststatus')->insert(['id' => '4','nameAr' =>'مازال تحت المراجعة','nameEn' => 'UnderReview Opened','key' => 'UnderReview_Opened']);
        DB::table('requeststatus')->insert(['id' => '5','nameAr' =>'مرتجع','nameEn' => 'Returned','key' => 'Returned']);
        DB::table('requeststatus')->insert(['id' => '6','nameAr' =>'موافقة','nameEn' => 'Accepted','key' => 'Accepted']);
        DB::table('requeststatus')->insert(['id' => '7','nameAr' =>'تأكيد الموافقة','nameEn' => 'AcceptanceConfirmed','key' => 'AcceptanceConfirmed']);
        DB::table('requeststatus')->insert(['id' => '8','nameAr' =>'تأكيد الرفض ','nameEn' => 'DeclineConfirmed','key' => 'DeclineConfirmed']);
        DB::table('requeststatus')->insert(['id' => '9','nameAr' =>'صحيح','nameEn' => 'Validated','key' => 'Validated']);
        DB::table('requeststatus')->insert(['id' => '10','nameAr' =>'تم الموافقة عليه من موظف اتحاد الصناعات','nameEn' => 'FEI_Approved','key' => 'FEI_Approved']);
        DB::table('requeststatus')->insert(['id' => '11','nameAr' =>'مرفوض','nameEn' => 'Declined','key' => 'Declined']);
        DB::table('requeststatus')->insert(['id' => '12','nameAr' =>'جاهز','nameEn' => 'Issued','key' => 'Issued']);
        DB::table('requeststatus')->insert(['id' => '13','nameAr' =>'مسودة','nameEn' => 'Draft','key' => 'Draft']);
       
    }
}
