<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\AccountRequest;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AccountRequestRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = AccountRequest::where('staff_id', $value)->first();

        if ($request) {
            if ($request->status === 'pending') {
                $fail('You already have a pending request. Please wait for HR to process it.');
            } elseif ($request->status === 'approved') {
                $fail('Your account has already been approved. Please contact HR if you need assistance.');
            } elseif ($request->status === 'rejected') {
                $fail('Your previous request was rejected. Please contact HR for more information.');
            }
        }
    }
} 