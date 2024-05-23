<?php

namespace App\Services;


use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Http\Requests\Auth\RegisterVerifyUserRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangeEmailSubmitRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    const CHANGE_EMAIL_CACHE_KEY = 'change.email.for.user.';

    public static function registerNewUser(RegisterNewUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $field = $request->getFieldName();
            $value = $request->getFieldValue();

            // بررسی اینکه آیا کاربر از قبل وجود دارد
            if ($user = User::where($field, $value)->first()) {
                // بررسی اینکه آیا کاربر قبلاً ثبت‌نام خود را کامل کرده است
                if ($user->verified_at) {
                    throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید');
                }

                return response(['message' => 'کد فعالسازی قبلا برای شما ارسال شده'], 200);
            }

            $code = random_verification_code();

            // ایجاد و ذخیره کاربر جدید
            $user = User::create([
                $field => $value,
                'verify_code' => $code,
            ]);

            // اطمینان از اینکه کاربر ایجاد شده
            if (!$user) {
                throw new Exception('کاربر ایجاد نشد');
            }



            Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', compact('code'));
            DB::commit();
            return response(['message' => 'کاربر ثبت موقت شد'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof UserAlreadyRegisteredException) {
                throw $e;
            }
            Log::error($e);
            return response(['message' => 'خطایی رخ داده است. لطفا دوباره تلاش کنید'], 500);
        }
    }

    public static function registerNewUserVerify(RegisterVerifyUserRequest $request)
    {
        $field = $request->has('email') ? 'email' : 'mobile';
        $code = $request->code;

        $user = User::where([
            $field => $request->input($field),
            'verify_code' => $code,
        ])->first();

        if (empty($user)) {
            throw new ModelNotFoundException('کاربری با اطلاعات مورد نظر یافت نشد');
        }

        $user->verify_code = null;
        $user->verified_at = now();
        $user->save();

        return response($user, 200);
    }

    public static function resendVerificationCodeToUser(ResendVerificationCodeRequest $request)
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
                'message' => 'کد مجدداً برای شما ارسال گردید'
            ], 200);
        }

        throw new ModelNotFoundException('کاربری با این مشخصات یافت نشد یا قبلا فعالسازی شده است');
    }

    public static function changeEmail(ChangeEmailRequest $request)
    {
        try {
            $email = $request->email;
            $userId = auth()->id();
            $code = random_verification_code();
            $expireDate = now()->addMinutes(config('auth.change_email_cache_expiration', 1440));
            Cache::put(self::CHANGE_EMAIL_CACHE_KEY . $userId, compact('email', 'code'), $expireDate);
            //TODO: ارسال ایمیل به کاربر برای تغییر ایمیل
            Log::info('SEND-CHANGE-EMAIL-CODE', compact('code'));

            return response([
                'message' => 'ایمیلی به شما ارسال شد لطفا صندوق دریافتی خود را بررسی نمایید'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response([
                'message' => 'خطایی رخ داده است و سرور قادر به ارسال کد فعالسازی نمیباشد'
            ], 500);
        }
    }

    public static function changeEmailSubmit(ChangeEmailSubmitRequest $request){
        $userId = auth()->id();
        $cacheKey = self::CHANGE_EMAIL_CACHE_KEY . $userId;
        $cache = Cache::get($cacheKey);
        if (empty($cache) || $cache['code'] != $request->code) {
            return response([
                'message' => 'درخواست نامعتبر'
            ], 400);
        }

        $user = auth()->user();
        $user->email = $cache['email'];
        $user->save();
        Cache::forget($cacheKey);
        return response([
            'message' => 'ایمیل با موفقیت تغییر یافت'
        ], 200);
    }
}
