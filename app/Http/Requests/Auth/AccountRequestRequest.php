<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\AccountRequestRule;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => ['required', 'string', 'max:255', new AccountRequestRule],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
} 