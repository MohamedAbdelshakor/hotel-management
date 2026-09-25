<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceptionistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $receptionist = $this->route('receptionist');

        return $receptionist instanceof User
            ? ($this->user()?->can('update', $receptionist) ?? false)
            : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $receptionist = $this->route('receptionist');
        $receptionistId = $receptionist instanceof User ? $receptionist->id : $receptionist;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($receptionistId)],
            'password' => ['nullable', 'string', 'min:6'],
            'national_id' => ['required', 'string', 'max:50', Rule::unique('users', 'national_id')->ignore($receptionistId)],
            'avatar_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
