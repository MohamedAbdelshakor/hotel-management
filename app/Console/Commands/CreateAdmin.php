<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('create:admin {--name= : The name of the admin} {--email= : The email of the admin} {--password= : The password for the admin}')]
#[Description('Create a new admin user')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $nameInput = $this->option('name');
        $emailInput = $this->option('email');
        $passwordInput = $this->option('password');

        // Handle case where --name is passed as an email (e.g. --name=admin2@admin.com)
        if (! $emailInput && $nameInput && filter_var($nameInput, FILTER_VALIDATE_EMAIL)) {
            $emailInput = $nameInput;
            $nameInput = ucfirst(explode('@', $nameInput)[0]);
        }

        $name = $nameInput ?: $this->ask('Enter admin name', 'Admin');
        $email = $emailInput ?: $this->ask('Enter admin email');
        $password = $passwordInput ?: $this->secret('Enter admin password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        $this->info("Admin user [{$admin->email}] created successfully with the 'admin' role.");

        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            [[$admin->id, $admin->name, $admin->email, 'admin']]
        );

        return self::SUCCESS;
    }
}
