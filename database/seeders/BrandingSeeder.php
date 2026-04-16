<?php

namespace Database\Seeders;

use App\Models\BrandingSetting;
use Illuminate\Database\Seeder;

class BrandingSeeder extends Seeder
{
    public function run(): void
    {
        BrandingSetting::firstOrCreate([], [
            'company_name' => 'Bayside Pavers',
            'logo_path' => null,
        ]);
    }
}
