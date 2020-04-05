<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Setting;

class FillSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore Setting';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $setting = new Setting;
        $setting->name = 'Kasir Kita';
        $setting->address = '';
        $setting->thousand_separator = '.';
        $setting->decimal_separator = ',';
        $setting->logo_remove = '';
        $setting->phone_number = '';
        $setting->divider = '';
        $setting->currency = '';
        $setting->tax = 0;
        $setting->printer = 'http://localhost:4000';
        $setting->save();
    }
}
