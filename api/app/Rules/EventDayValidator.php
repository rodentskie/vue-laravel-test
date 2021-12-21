<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EventDayValidator implements Rule
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
        $valid = [
            'M',
            'T',
            'W',
            'Th',
            'F',
            'S',
            'Su',
        ];
        $split = explode(',', $value);
        $check = true;
        foreach ($split as $e) {
            $bool = in_array($e, $valid);
            if (!$bool) {
                $check = false;
                break;
            }
        }
        return $check;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid days format!';
    }
}
