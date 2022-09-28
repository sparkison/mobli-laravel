<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    private $id;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = $this->route('user');

        return true; // only concerned with rules, ignore authorize for now
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];
        switch ($this->method()) {
            case 'POST':
                $rules['email'] = ['required', 'email', 'max:255', 'unique:users'];
                break;
            case 'PATCH':
                $rules['email'] = [
                    'required', 'email', 'max:255',
                    Rule::unique('users')->ignore($this->id)
                ];
                break;
        }
        return $rules;
    }
}
