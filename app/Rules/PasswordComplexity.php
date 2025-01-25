<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordComplexity implements Rule
{
    /**
     * Determine if the password is valid.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Check for at least one number
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        // Check for at least one special character
        if (!preg_match('/[@$!%*?&]/', $value)) {
            return false;
        }

        // Check for a minimum length of 8 characters
        if (strlen($value) < 8) {
            return false;
        }

        return true;
    }

    /**
     * Get the error message for the failed validation.
     *
     * @return string
     */
    public function message()
    {
        return 'The password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and be at least 8 characters long.';
    }
}
