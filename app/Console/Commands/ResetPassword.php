<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Command
{
    protected $signature = 'reset:password {--for=}';

    protected $description = 'Reset Password to Default 12345';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = $this->option('for');

        $this->newLine();
        $this->info("Email: {$email}.");

        $user = User::query()->firstWhere('email', $email);
        if (!$user) {
            $this->error("No user found!");
            return;
        }

        if (app()->isProduction()) {
            $this->info('The application is in Production.');
            if ($this->confirm('Do you wish to continue?')) {
                $user->password = Hash::make('12345');
                $user->save();

                $this->info("Password Reset Successful for {$user->name}.");
            } else {
                $this->info('Password Reset Cancelled.');
            }
        } else {
            $user->password = Hash::make('12345678');
            $user->save();

            $this->info("Password Reset Successful for {$user->name}.");
        }
    }
}
