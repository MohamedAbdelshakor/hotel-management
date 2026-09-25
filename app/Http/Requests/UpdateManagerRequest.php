<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $manager = $this->route('manager');
        $managerId = $manager instanceof User ? $manager->id : $manager;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($managerId)],
            'password' => ['nullable', 'string', 'min:6'],
            'national_id' => ['required', 'string', 'max:50', Rule::unique('users', 'national_id')->ignore($managerId)],
            'avatar_image' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
        ];
    }
}
