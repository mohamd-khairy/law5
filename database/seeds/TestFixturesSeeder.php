<?php

use Illuminate\Database\Seeder;

class TestFixturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            'TestUsersFixtures',
        ]);
    }
}
