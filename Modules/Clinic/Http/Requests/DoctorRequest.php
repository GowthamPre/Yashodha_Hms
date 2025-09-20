<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            // 'password' => 'required|min:8',
            'password' => [
                        'required',
                        'string',
                        'min:6',             // must be at least 10 characters in length
                        'regex:/[a-z]/',      // must contain at least one lowercase letter
                        'regex:/[A-Z]/',      // must contain at least one uppercase letter
                        'regex:/[0-9]/',      // must contain at least one digit
                        'regex:/[@$!%*#?&]/', // must contain a special character
                    ],
            'confirm_password' => 'required|same:password',
            'mobile' => 'required|string',
            'commission_id' => 'required',
            'clinic_id' => 'required',
            'service_id' => 'required',
            // Define other validation rules for your fields
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must contain at least one uppercase / one lowercase / one number and one symbol.',            
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
