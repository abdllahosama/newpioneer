<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class UpdateStoreIds extends Command
{
    protected $signature = 'update:store-ids';
    protected $description = 'Update store_id in tickets table based on user_id';

    public function handle()
    {
        $this->info('Updating store_ids in tickets table...');
        $tickets = DB::table('tickets')->whereNull('store_id')->get();
        foreach ($tickets as $ticket) {
            $store_id = $this->getStoreIdForUserId($ticket->user_id);
            // Update the store_id in the tickets table
            DB::table('tickets')->where('id', $ticket->id)->update(['store_id' => $store_id]);
        }
        $this->info('Store_ids updated successfully.');
    }

    private function getStoreIdForUserId($user_id)
    {
        return DB::table('users')->where('id', $user_id)->value('store_id');
    }
}
