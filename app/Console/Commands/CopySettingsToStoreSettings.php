<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopySettingsToStoreSettings extends Command
{
    protected $signature = 'copy:settings';

    protected $description = 'Copy specific columns from settings to store_settings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $columnNames = \Schema::getColumnListing('settings');
        $filteredColumnNames = array_diff($columnNames, ['store_id', 'id']);

        $settings = \DB::table('settings')->get();

        if ($settings->count() > 0) {
            foreach ($settings as $oneSetting) {
                foreach ($filteredColumnNames as $setting) {
                    if ($oneSetting->$setting == null || $oneSetting->$setting == '') continue;
                    \DB::table('store_settings')->insert([
                        'key' => $setting,
                        'value' => $oneSetting->$setting,
                        'store_id' => $oneSetting->store_id,
                    ]);
                }
            }
            $this->info('Data copied to store_settings successfully!');
        } else {
            $this->error('No data found.');
        }

    }
}
