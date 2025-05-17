<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

trait HasAlerts
{
    /**
     * Flash a success message to the session.
     */
    protected function success(string $message): void
    {
        Session::flash('alert', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    /**
     * Flash an error message to the session.
     */
    protected function error(string $message): void
    {
        Session::flash('alert', [
            'type' => 'error',
            'message' => $message
        ]);
    }

    /**
     * Flash a warning message to the session.
     */
    protected function warning(string $message): void
    {
        Session::flash('alert', [
            'type' => 'warning',
            'message' => $message
        ]);
    }

    /**
     * Flash an info message to the session.
     */
    protected function info(string $message): void
    {
        Session::flash('alert', [
            'type' => 'info',
            'message' => $message
        ]);
    }

    /**
     * Return a redirect response with an alert.
     */
    protected function redirectWithAlert(string $route, string $message, string $type = 'success'): RedirectResponse
    {
        $this->{$type}($message);
        return redirect()->route($route);
    }
} 