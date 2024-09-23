<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AclSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RolesEnum::cases() as $role) {
            Role::create(['name' => $role]);
        }
    }
}
