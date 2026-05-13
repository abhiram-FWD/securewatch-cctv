<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckShift
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, ['guard', 'manager'])) {
            $user = Auth::user();
            $isOnDuty = $user->isOnDuty();
            $shiftRecord = \App\Models\Shift::where('name', 'LIKE', "%{$user->shift}%")->first();

            session([
                'is_on_duty' => $isOnDuty,
                'shift_start' => $shiftRecord?->start_time,
                'shift_end' => $shiftRecord?->end_time,
            ]);
        }
        
        return $next($request);
    }
}
