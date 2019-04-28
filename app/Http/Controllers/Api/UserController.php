<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\User;
use CodeShopping\Common\OnlyTrashed;
use CodeShopping\Http\Requests\UserRequest;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\UserResource;
use CodeShopping\Events\UserCreatedEvent;

class UserController extends Controller
{

    use OnlyTrashed;

    public function index(Request $request)
    {
        $query = User::query();
        $query = $this->onlyTrashedIfRequest($request, $query);
        $users = $query->paginate(10);
        return UserResource::Collection($users);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        event(new UserCreatedEvent($user));
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->fill($request->all());
        $user->save();
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([],204);
    }

}
