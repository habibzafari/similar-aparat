<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class RegisterVerificationException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request)
    {
        return response()->json([
            'error' => 'کد تاییده وارد شده اشتباه میباشد'
        ], 400);
    }
}
