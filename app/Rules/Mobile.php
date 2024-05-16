<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Mobile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mobileRegex = '~^(0098|\+?98|0)9\d{9}$~';
        if (!preg_match($mobileRegex, $value)) {
            $fail('شماره موبایل وارد شده اشتباه می‌باشد');
        }
        // preg_match($mobileRegex, $value, $maches);
        // if (!empty($maches)) {
        //     $fail('شماره موبایل وارد شده اشتباه میباشد');
        // }
    }

    
}
