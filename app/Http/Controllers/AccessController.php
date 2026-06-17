<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessCodeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessController extends Controller
{
    public function show(): View
    {
        return view('access.show');
    }

    public function store(AccessCodeRequest $request): RedirectResponse
    {
        $code = config('app.admin_access_code');

        if ($request->validated('access_code') !== $code) {
            return back()->withErrors(['access_code' => 'Invalid access code.'])->withInput();
        }

        $request->session()->put('admin_unlocked', true);

        return redirect()->route('admin.dashboard');
    }
}
