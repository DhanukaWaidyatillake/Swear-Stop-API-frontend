<?php

namespace App\Console\Commands;

use App\Models\SiteConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RefreshSignupToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signup_secret:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the signup token in the configs table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SiteConfig::updateOrCreate(['key' => 'signup_secret'], [
            'value' => Str::uuid()->toString()
        ]);
    }
}
