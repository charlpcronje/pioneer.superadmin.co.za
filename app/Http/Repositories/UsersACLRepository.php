<?php


namespace App\Http\Repositories;


use Alexusmai\LaravelFileManager\Services\ACLService\ACLRepository;

class UsersACLRepository implements ACLRepository
{

    public function getUserID()
    {
        // TODO: Implement getUserID() method.
        return \Auth::id();
    }

    public function getRules(): array
    {
        // TODO: Implement getRules() method.
        if (\Auth::id() === 1) {
            return [
                ['disk' => 'dropbox', 'path' => '*', 'access' => 2],
            ];
        }

    }
}
