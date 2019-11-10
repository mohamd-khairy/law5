<?php

use App\Model\User;
use App\Model\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUsersFixtures extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->truncate();

        $roles = DB::select("Select * from roles");

        foreach ($roles as $role) {
            $id = DB::table('users')->insertGetId([
                'roleId' => $role->id,
                'name' => "Test {$role->nameEn}",
                'telephone' => "01234567891",
                'email' => "{$role->key}@example.com",
                'token' => User::generateApiToken(),
                'password' => Hash::make("password"),
                'isEmailVerified' => true,
                'isActive' => true,
                'sectorId' => null,
                'resetPasswordCode' => null,
                'resetPasswordCodeCreationdate' => null,
                'step2token' => null,
                'step2code' => null,
                'codeCreationDate' => null ,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
            
            // UserSetting::create(['userId' , $id]);
        }
    }
}
