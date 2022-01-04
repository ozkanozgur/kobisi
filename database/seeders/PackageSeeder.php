<?php

namespace Database\Seeders;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->format('Y-d-m H:i:s');

        $packages = [
            [
                'name'          => 'Monthly',
                'period'        => 1,
                'price'         => 99.90,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'name'          => 'Yearly',
                'period'        => 12,
                'price'         => 990.00,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        Package::insert($packages);
    }
}
