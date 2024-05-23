<?php

namespace App\Observers;
use Illuminate\Support\Str;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // dd('created logs for users');
        // ذخیره کانال برای کاربر جدید
        $channelName = !empty($user->email)
            ? Str::before($user->email, '@')
            : Str::after($user->mobile, '+98');

        $user->channel()->create(['name' => $channelName]);
        // $channelName = $field === 'mobile' ? substr($value, 3) : explode('@', $value)[0];
        // $channelName = $field === 'mobile' ? str_after($value, '+98') : str_before($value, '@');
        // $channel = $user->channel()->create(['name' => $channelName, 'user_id' => $user->id]);

        // اطمینان از اینکه کانال ایجاد شده
        // if (!$channel) {
        //     throw new Exception('کانال ایجاد نشد');
        // }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
