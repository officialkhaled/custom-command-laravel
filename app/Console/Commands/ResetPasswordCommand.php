<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPasswordCommand extends Command
{
    protected $signature = 'reset:password {--for=} {--new_password=}';

    protected $description = 'Reset Password to Default 12345';

    public function handle(): void
    {
        $email = $this->option('for');
        $newPassword = $this->option('new_password');

        $this->newLine();
        $this->info("Email: {$email}.");

        $user = User::query()->firstWhere('email', $email);
        if (!$user) {
            $this->error("No user found!");
            return;
        }

        if (!$newPassword) {
            $newPassword = '12345678';
        }

        if (app()->isProduction()) {
            $this->info('The application is in Production.');
            if ($this->confirm('Do you wish to continue?')) {
                $user->password = Hash::make($newPassword);
                $user->save();

                $this->info("Password Reset Successful for {$user->screen_name}.");
            } else {
                $this->info('Password Reset Cancelled.');
            }
        } else {
            $user->password = Hash::make($newPassword);
            $user->save();

            $this->info("Password Reset Successful for {$user->screen_name}.");
            $this->newLine();
        }
    }

}
