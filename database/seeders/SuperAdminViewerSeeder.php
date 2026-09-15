<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminViewerSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('roles')->where('id', 4)->doesntExist()) {
            DB::table('roles')->insert([
                'id' => 4,
                'name' => 'superadmin_viewer',
            ]);
        }

        User::updateOrCreate(
            ['email' => 'superadmin@siatrack.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Overseer',
                'id_number' => 'SIA-VIEWER',
                'password' => Hash::make('Siatr@ck2026'),
                'role_id' => 4,
                // TINANGGAL NA NATIN ANG 'email_verified_at' DITO
            ]
        );
    }
}