<?php

use App\Model\User;
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
            DB::table('users')->insert([
                'roleId' => $role->id,
                'name' => "Test {$role->nameEn}",
                'telephone' => "01234567891",
                'email' => "{$role->key}@example.com",
                'token' => User::generateApiToken(),
                'password' => Hash::make("password"),
                'isEmailVerified' => true,
                'isActive' => true,
            ]);
        }
    }
}
