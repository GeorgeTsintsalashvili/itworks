<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StatementsSchedule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pattern = '/^(\[\*\]|\[\*\]\:[1-6]\d?|\[\d{1,2}(\|\d{1,2}){0,23}\]|\[\d{1,2}(\|\d{1,2}){0,23}\]\:[1-9]\d?|\[\d{1,2}\-\d{1,2}(\|\d{1,2}\-\d{1,2}){0,23}\]|\[\d{1,2}\-\d{1,2}(\|\d{1,2}\-\d{1,2}){0,23}\]:\d{1,2})$/';

        return is_string($value) && preg_match($pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
