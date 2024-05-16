<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RegisterVerificationException;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Http\Requests\Auth\RegisterVerifyUserRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FacadesRequest;

class AuthController extends Controller
{
    public function register(RegisterNewUserRequest $request)
    {
        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $code = random_int(100000, 999999);
        if ($email && $mobile) {
            $user = User::where('email', $email)->orWhere('mobile', $mobile)->first();
            if ($user) {
                if ($user->verified_at) {
                    throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید');
                }
                return response(['message' => 'کاربر با این اطلاعات قبلاً ثبت‌ نام کرده است'], 400);
            }

            User::create([
                'email' => $email,
                'mobile' => $mobile,
                'verify_code' => $code,
            ]);


            return response(['message' => 'کاربر ثبت موقت شد'], 200);
        }

        if ($email) {
            $field = 'email';
            $value = $email;
        } elseif ($mobile) {
            $field = 'mobile';
            $value = $mobile;
        } else {
            return response(['error' => 'لطفا ایمیل یا شماره موبایل را وارد کنید'], 400);
        }
        if ($field === 'mobile') {
            $value = to_valid_mobile_number($value);
        }

        // dd($value,$field);
        if ($user = User::where($field, $value)->first()) {
            return response(['message' => 'کد فعالسازی قبلا برای شما ارسال شده است'], 200);
        }

        User::create([
            $field => $value,
            'verify_code' => $code,
        ]);

        return response(['message' => 'کاربر ثبت موقت شد'], 200);
    }


    public function  registerVerify(RegisterVerifyUserRequest $request)
    {
        $field = $request->has('email') ? 'email' : 'mobile';
        $code = $request->code;
        $user = User::where([
            $field => $request->input($field),
            'verify_code' => $code,
        ])->first();
        if (empty($user)) {
            throw new ModelNotFoundException('کاربری با کد مورد نظر یافت نشد');
        }
        $user->verify_code = null;
        $user->verified_at = now();
        $user->save();
        return response($user, 200);
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request)
    {
        $field = $request->getFieldName();
        $value = $request->getFieldValue();

        $user = User::where($field, $value)->whereNull('verified_at')->first();

        if (!empty($user)) {
            $dateDiff = now()->diffInMinutes($user->updated_at);

            // اگر زمان مورد نظر از ارسال کد قبلی گذشته بود مجددا کد جدید ایجاد و ارسال میکنیم
            if ($dateDiff > config('auth.resend_verification_code_time_diff', 60)) {
                $user->verify_code = random_verification_code();
                $user->save();
            }

            //TODO: ارسال ایمیل یا پیامک به کاربر
            Log::info('RESEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => $user->verify_code]);

            return response([
                'message' => 'کد مجدداً برای شما ارسال گردید.'
            ], 200);
        }

        throw new ModelNotFoundException('کاربری با این مشخصات یافت نشد یا قبلا فعالسازی شده است');
    }
}
