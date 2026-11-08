<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Бізнес-правило Use Case «Опублікувати допис»: створювати дописи
 * можуть лише користувачі з роллю Author або Admin (заголовок X-Author-Role).
 */
class CheckAuthorRole
{
    private const ALLOWED_ROLES = ['author', 'admin'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post')) {
            if (! $request->hasHeader('X-Author-Role')) {
                return response()->json([
                    'status' => 'access_denied',
                    'message' => 'Відхилено: відсутній обов\'язковий заголовок X-Author-Role.',
                ], 403);
            }

            $role = strtolower((string) $request->header('X-Author-Role'));

            if (! in_array($role, self::ALLOWED_ROLES, true)) {
                return response()->json([
                    'status' => 'forbidden_role',
                    'message' => 'Відхилено: публікувати дописи можуть лише Author або Admin.',
                ], 403);
            }
        }

        $response = $next($request);

        // Пост-обробка: службовий діагностичний заголовок
        $response->headers->set('X-Security-Policy', 'Enforced');

        return $response;
    }
}
