<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $userId = $user ? $user->id : null;
        
        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
        ];
        
        // Only add unique rule if user exists
        if ($userId) {
            $emailRules[] = Rule::unique(User::class)->ignore($userId);
        } else {
            $emailRules[] = Rule::unique(User::class);
        }
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:10240'], // 10MB max
        ];
    }
}
