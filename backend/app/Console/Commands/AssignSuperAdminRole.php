<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignSuperAdminRole extends Command
{
    protected $signature = 'user:assign-super-admin {user_id?}';
    protected $description = 'Assign Super Admin role to a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // Get first user if no ID provided
            $user = User::first();
            if (!$user) {
                $this->error('No users found in the database.');
                return;
            }
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return;
            }
        }

        try {
            $user->assignRole('Super Admin');
            $this->info("Super Admin role assigned to: {$user->name} ({$user->email})");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
