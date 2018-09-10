<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUser;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as ValidatorInterface;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    /**
     * @var AccountRepositoryInterface
     */
    protected $account;

    /**
     * AccountController constructor.
     *
     * @param UserRepositoryInterface $user
     * @param AccountRepositoryInterface $account
     */
    public function __construct(
        UserRepositoryInterface $user,
        AccountRepositoryInterface $account
    )
    {
        $this->user = $user;

        $this->account = $account;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        return UserResource::collection(
            $this->user->model()->get()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return UserResource
     */
    public function show($id)
    {
        return new UserResource($this->user->findById($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUser $request
     * @return UserResource
     */
    public function store(StoreUser $request)
    {
        return new UserResource($this->user->create($request->all()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUser $request
     * @param  int $id
     * @return UserResource
     */
    public function update(StoreUser $request, $id)
    {
        $this->user->update($id, $request->all());

        // Re-fetch the model so that it reflects the updates.

        return new UserResource($this->user->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->user->findById($id);
        $user->delete();

        return response(['success' => true]);
    }
}
