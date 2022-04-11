<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ChatApp\Repos\AccountVerification\EmailVerification;
use App\Http\Requests\Auth\EmailVerificationRequest;

class AccountVerificationController extends Controller
{
    // @todo get mail status (sent or not) and return correct message
    public function createOrUpdateForEmail(EmailVerificationRequest $request)
    {
        $verification = (new EmailVerification)->createOrUpdate($request->email);

        return response()->json(['success' => __('Email has been sent. Check your inbox.')]);
    }
}
