<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        // Allow teachers access to attendance and exam related routes
        if (Auth::user()->role === 'teacher') {
            $allowedRoutes = [
                'admin.dashboard',
                'admin.attendance.index',
                'admin.attendance.create', 
                'admin.attendance.view',
                'admin.exams.index',
                'admin.exams.show',
                'admin.exams.create',
                'admin.exams.edit',
                'admin.students.create',
                'admin.exams.marking',
                'admin.exams.student.details',
                'admin.subjects.index',
                'admin.subjects.create',
                'admin.subjects.edit',
                'admin.subjects.view',
                'admin.batches.index',
                'admin.batches.create',
                'admin.batches.edit',
                'admin.courses.index',
                'admin.courses.create',
                'admin.courses.edit',
                'admin.courses.view',
                'admin.students.index',
                'student.profile',
                'student.edit'
            ];
            
            $currentRoute = $request->route()->getName();
            if (!in_array($currentRoute, $allowedRoutes)) {
                abort(403, 'Teachers can only access attendance and exam related features.');
            }
        }
        
        return $next($request);
    }
}
