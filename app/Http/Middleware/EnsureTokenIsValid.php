<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            Log::warning('No token provided in request');
            return response()->json([
                'success' => false,
                'message' => 'لم يتم توفير رمز الوصول',
                'data' => null
            ], 401);
        }
        try {
            if (!Auth::guard('sanctum')->check()) {
                Log::warning('Invalid or expired token', ['token' => substr($token, -10) . '...']);
                return response()->json([
                    'success' => false,
                    'message' => 'رمز الوصول غير صالح أو منتهي الصلاحية',
                    'data' => null
                ], 401);
            }
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Token validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحقق من صحة رمز الوصول',
                'data' => null
            ], 500);
        }
    }
}
