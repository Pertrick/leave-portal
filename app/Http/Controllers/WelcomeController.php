<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'showRegBtn' => env('SHOW_REG_BTN', false),
            'companyName' => env('COMPANY', 'Leave Portal')
        ]);
    }
} 