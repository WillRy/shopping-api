<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use CodeShopping\Http\Requests\UserRequest;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\UserResource;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();
        $query = $this->onlyTrashedIfRequest($request, $query);
        $users = $query->paginate(10);
        return UserResource::Collection($users);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {

    }


    public function destroy($id)
    {
        //
    }

    private function onlyTrashedIfRequest(Request $request, Builder $query)
    {
        if ($request->get('trashed') == 1) {
            $query = $query->onlyTrashed();
        }
        return $query;
    }
}
