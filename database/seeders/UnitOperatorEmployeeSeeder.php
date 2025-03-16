<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\User;
use App\Models\Pic;
use Illuminate\Support\Facades\Hash;

class UnitOperatorEmployeeSeeder extends Seeder
{
    public function run()
    {

        $operator = User::create([
            'username' => 'operator_lingkungan',
            'name' => 'opt2',
            'email' => 'operator.lingkungan@example.com',
            'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            'role_id' => 2, // Operator
            'unit_id' => 2,
        ]);

        // 3. Buat Pegawai (PIC) untuk Unit Lingkungan (role_id = 3)
        $employee = User::create([
            'username' => 'pegawai_lingkungan',
            'email' => 'pegawai.lingkungan@example.com',
            'name' => 'pegawai_2',
            'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            'role_id' => 3, // Pegawai (PIC)
            'unit_id' => 2,
        ]);
    }
}