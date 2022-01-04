<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->format('Y-d-m H:i:s');
        $faker = Faker::create();

        for($i=0; $i<1000; $i++){
            $company = new Company();
            $company->site_url      = $faker->url;
            $company->name          = $faker->firstName;
            $company->last_name     = $faker->lastName;
            $company->company_name  = $faker->company();
            $company->email         = $faker->unique()->safeEmail;
            $company->password      = Hash::make('12345678');
            $company->created_at    = $now;
            $company->updated_at    = $now;
            $company->save();

            $company->createToken('Kobisi')->plainTextToken;
        }
    }
}
