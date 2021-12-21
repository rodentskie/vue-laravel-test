<?php

namespace App\Http\Requests;

use App\Rules\EventDayValidator;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string'
            ],
            'days' => [
                'required',
                'string',
                new EventDayValidator(),
            ],
            'from_date' => [
                'required',
                'date'
            ],
            'to_date' => [
                'required',
                'date'
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter an event name!',
            'days.required' => 'Please enter a days!',
            'from_date.required' => 'Please enter start date!',
            'from_date.date' => 'Please enter a valid start date!',
            'to_date.required' => 'Please enter end date!',
            'to_date.date' => 'Please enter a valid end date!',
        ];
    }
}
