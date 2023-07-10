<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateGod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'god:create {--name=} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        if(!$this->option('name')){
            abort(422, 'Name option is required');
        }

        if(!$this->option('email')){
            abort(422, 'Email option is required');
        }

        if(!$this->option('password')){
            abort(422, 'Password option is required');
        }

        $roleGod = Role::findByName('god', 'api');

        if(!$roleGod){
            abort(422, 'Role god does not exists.Please run seeds to create roles.');
        }

        $god = User::create([
            'name' => $this->option('name'),
            'email' => $this->option('email'),
            'password' => Hash::make($this->option('password'))
        ]);


        $god->assignRole($roleGod);
        $god->givePermissionTo('revoke-impersonate');

        return 'success';
    }
}
