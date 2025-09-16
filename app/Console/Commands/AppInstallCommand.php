<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppInstallCommand extends Command
{
    protected $signature = 'app:install';

    protected $description = 'Install Application Requirements';

    public function handle(): void
    {
        if (!\File::exists(public_path('storage'))) {
            $this->call('storage:link');
        }
        $this->call('key:generate');
        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->call('countries:fetch');
        $this->call('passport:install',['--force' => true]);
        $this->call('optimize:clear');
    }
}
