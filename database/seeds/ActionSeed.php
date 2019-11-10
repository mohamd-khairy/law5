<?php

use Illuminate\Database\Seeder;

class ActionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('actions')->truncate();
        DB::table('actions')->insert(['id' => '1','nameEn' => 'Create', 'nameAr' => 'إنشاء', 'key' => 'Create']);
        DB::table('actions')->insert(['id' => '2','nameEn' => 'Assign', 'nameAr' => 'تعيين', 'key' => 'Assign']);
        DB::table('actions')->insert(['id' => '3','nameEn' => 'Start review', 'nameAr' => 'ابدأ المراجعة', 'key' => 'Start review']);
        DB::table('actions')->insert(['id' => '4','nameEn' => 'Save', 'nameAr' => 'حفظ', 'key' => 'Save']);
        DB::table('actions')->insert(['id' => '5','nameEn' => 'End Review', 'nameAr' => 'تمت المراجعه', 'key' => 'End Review']);
        DB::table('actions')->insert(['id' => '6','nameEn' => 'AutoEndReview', 'nameAr' => 'تمت المراجه تلقائيا', 'key' => 'AutoEndReview']);
        DB::table('actions')->insert(['id' => '7','nameEn' => 'View', 'nameAr' => 'يراجع', 'key' => 'View']);
        DB::table('actions')->insert(['id' => '8','nameEn' => 'Accept', 'nameAr' => 'مقبول', 'key' => 'Accept']);
        DB::table('actions')->insert(['id' => '9','nameEn' => 'Confirm', 'nameAr' => 'تأكيد', 'key' => 'Confirm']);
        DB::table('actions')->insert(['id' => '10','nameEn' => 'Approve', 'nameAr' => 'موافقة', 'key' => 'Approve']);
        DB::table('actions')->insert(['id' => '11','nameEn' => 'Return', 'nameAr' => 'رجوع', 'key' => 'Return']);
        DB::table('actions')->insert(['id' => '12','nameEn' => 'Validate', 'nameAr' => 'التحقق من صحة', 'key' => 'Validate']);
        DB::table('actions')->insert(['id' => '13','nameEn' => 'Decline', 'nameAr' => 'مرفوض', 'key' => 'Decline']);
        DB::table('actions')->insert(['id' => '14','nameEn' => 'ChangeChamber', 'nameAr' => 'تغير الغرفه', 'key' => 'ChangeChamber']);
        DB::table('actions')->insert(['id' => '15','nameEn' => 'ReturnToEmployee', 'nameAr' => 'ارجاعه للموظف', 'key' => 'ReturnToEmployee']);
        DB::table('actions')->insert(['id' => '16','nameEn' => 'Resend', 'nameAr' => 'اعاده ارسال', 'key' => 'Resend']);
        DB::table('actions')->insert(['id' => '17','nameEn' => 'Issue Certificates', 'nameAr' =>'اصدار الشهادات', 'key' => 'IssueCertificates']);
    }
}
