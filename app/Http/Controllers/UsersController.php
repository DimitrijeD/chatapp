<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatApp\Repos\User\UserEloquentRepo;

use App\Http\Requests\Users\GetMissingUsersRequest;

class UsersController extends Controller
{
    /**
     * User has some or no users in store
     * He requests for users by str and gives controller ID-s of users he has in store
     * Controller returns only those users:
     *      he doesn't have ,
     *      which have email verified,
     *      
     */
    public function getMissingUsers(GetMissingUsersRequest $request, UserEloquentRepo $userRepo)
    {
        // exclude self from q
        $ids = $request->i_have_ids;
        $ids[] = auth()->user()->id; 

        return response()->json($userRepo->getNetQuery($request->search_str, $ids));
    }
}
