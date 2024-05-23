<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangeEmailSubmitRequest;
use App\Services\UserService;


class UserController extends Controller
{


    public function changeEmail(ChangeEmailRequest $request)
    {
        return UserService::changeEmail($request);
    }
    public function changeEmailSubmit(ChangeEmailSubmitRequest $request)
    {
        return UserService::changeEmailSubmit($request);
    }
}
