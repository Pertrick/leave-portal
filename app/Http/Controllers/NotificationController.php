<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Show the notifications page
     */
    public function index()
    {
        return Inertia::render('Notifications');
    }
} 