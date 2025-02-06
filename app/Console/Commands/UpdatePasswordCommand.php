<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordCommand extends Command
{
    protected $signature = 'update:password';

    protected $description = 'Update User Password';

    public function handle(): void
    {
        $noOfUsers = text(label: 'Reset password for all users? (yes/no)', required: true);

        if (strtolower($noOfUsers) == 'yes') {
            $password = text(label: 'Enter the new password?', required: true);

            User::query()->update(['password' => Hash::make($password), 'last_pass_change_date' => date('Y-m-d')]);
            $this->alert("All Users Password Updated Successfully");
        } else {
            $defaultEmailCheck = text(label: "Do you want to update the Super Admin's password? (yes/no)", required: true);

            if (strtolower($defaultEmailCheck) == 'yes') {
                $email = 'admin@gmail.com';
            } else {
                $email = text(label: 'Enter the email?', required: true);
            }

            $user = User::query()->firstWhere('email', $email);
            if (!$user) {
                $this->error("No user found! Try again.");
                return;
            }

            $newPassword = text(label: 'Enter the new password?', required: true);

            if (app()->isProduction()) {
                $this->info('The application is in Production.');
                if ($this->confirm('Do you wish to continue?')) {
                    $user->password = Hash::make($newPassword);
                    $user->save();

                    $this->alert("Password Reset Successful for {$user->screen_name}.");
                } else {
                    $this->info('Password Reset Cancelled.');
                }
            } else {
                $user->password = Hash::make($newPassword);
                $user->save();

                $this->alert("Password Reset Successful for {$user->screen_name}.");
            }
        }
    }
}
