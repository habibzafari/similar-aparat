<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyRegisteredException extends Exception
{

    public function report(): void
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 400);
    }
}
