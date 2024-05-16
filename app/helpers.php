<?php

/**
 * Converts a mobile number to a valid Iranian mobile number format
 *
 * @param string $mobile The mobile number to convert
 * @return string The converted mobile number in +98xxxxxxxxxx format
 */

if (!function_exists('to_valid_mobile_number')) {
    function to_valid_mobile_number($mobile)
    {
        return  $mobile = '+98' . substr($mobile, -10, 10);
    }
}


/**
 * Generates a random 6-digit verification code
 *
 * @return int A random 6-digit integer
 */
if (!function_exists('random_verification_code')) {
    function random_verification_code()
    {
        return random_int(100000, 999999);
    }
}

// if (!function_exists('helper_function_name')) {
//     function helper_function_name($parameters)
//     {
//         // function code here
//     }
// }

