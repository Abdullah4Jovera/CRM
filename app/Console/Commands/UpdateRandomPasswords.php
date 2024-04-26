<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Console\Command;

class UpdateRandomPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:random-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update users table with random hashed passwords';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $randomSalt = 'random_salt';

        // DB::table('users')->update([
        //     'password' => Hash::make($randomSalt . random_int(1, 1000000))
        // ]);

            // here is my update 
        DB::table('users')
        ->where('email', 'kamal@jovera.ae')
        ->update([
            'password' => Hash::make($randomSalt . random_int(1, 1000000))
        ]);




        $this->info('Random passwords updated successfully.');
    }
}
