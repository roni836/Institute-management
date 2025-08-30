<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;

class ResolveDevice
{
    public function handle(Request $request, Closure $next)
    {
        $publicId = $request->cookie('adm_dev');

        if ($publicId) {
            // Attach the device (if any) to the request for easy access
            $request->attributes->set(
                'resolvedDevice',
                Device::where('public_id', $publicId)->first()
            );
        }

        return $next($request);
    }
}
