<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default roles (admin and profissional)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Global roles (team_id is null)
        $admin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'studio_id' => null]);
        $profissional = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'profissional', 'guard_name' => 'web', 'studio_id' => null]);

        $this->info('Roles "admin" and "profissional" created globally successfully.');
    }
}
