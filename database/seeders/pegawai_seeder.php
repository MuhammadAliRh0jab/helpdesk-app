<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class pegawai_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operator = User::create([
            'username' => 'operator_pendidikan',
            'name' => 'opt3',
            'email' => 'operator.pendidikann@example.com',
            'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            'role_id' => 2, // Operator
            'unit_id' => 3,
        ]);

        // 3. Buat Pegawai (PIC) untuk Unit Lingkungan (role_id = 3)
        $employee = User::create([
            'username' => 'pegawai_lingkungann',
            'email' => 'pegawai.lingkungann@example.com',
            'name' => 'pegawai_22',
            'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            'role_id' => 3, // Pegawai (PIC)
            'unit_id' => 2,
        ]);
        $employee = User::create([
            'username' => 'warga456',
            'email' => 'warga456@gmail.com',
            'name' => 'warga456',
            'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            'role_id' => 4, // Pegawai (PIC)
        ]);
        $employee = User::create([
            'username' => 'pegawai_kesehatan',
            'email' => 'pegawai.kesehatan@example.com',
            'name' => 'pegawai_3',
            'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            'role_id' => 3, // Pegawai (PIC)
            'unit_id' => 3,
        ]);
    }
}
