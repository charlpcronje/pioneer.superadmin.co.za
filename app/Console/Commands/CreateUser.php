<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pioneer:create_user';

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
        $username = "info@fgx.co.za";
        $password ="@fgx-\?)pion";
        $name = "FGX";
        $surname = "Admin";
        $phone_number ="0110512800";

        $user = User::create(['email' => $username,
            'password' => \Hash::make($password), 'name' => $name, 'surname' => $surname, 'phone_number' => $phone_number]);
        $role = Role::findByName('Admin');
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);


        return 0;
    }
}
