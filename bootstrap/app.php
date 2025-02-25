<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

function getStatusCodeFromException(Throwable $e)
{
  // Для исключений Symfony
  if ($e instanceof HttpExceptionInterface) {
    return $e->getStatusCode();
  }

  // Для кастомных исключений Laravel
  if ($e instanceof HttpResponseException) {
    return $e->getResponse()->getStatusCode();
  }

  // Для ValidationException
  if ($e instanceof ValidationException) {
    return 422;
  }

  // Для ModelNotFoundException
  if ($e instanceof ModelNotFoundException) {
    return 404;
  }

  if ($e instanceof AuthenticationException) {
    return 401;
  }

  // Стандартный код для остальных исключений
  return method_exists($e, 'getCode') && $e->getCode() >= 400 && $e->getCode() < 600
    ? $e->getCode()
    : 500;
}

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->statefulApi();
    $middleware->api(prepend: [
      \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
    $middleware->alias([
      'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
      'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
      'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->renderable(function (NotFoundHttpException $e, $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => $e->getMessage(),
          'error_code' => 404
        ], 404);
      }
    });

    // Обработка ModelNotFoundException
    $exceptions->renderable(function (ModelNotFoundException $e, $request) {
      if ($request->wantsJson()) {
        return response()->json([
          'message' => $e->getMessage(),
          'error_code' => 404
        ], 404);
      }
    });

    // Кастомная обработка ошибок валидации
    $exceptions->renderable(function (ValidationException $e, $request) {
      if ($request->expectsJson()) {
        return response()->json([
          'message' => $e->getMessage(),
          'errors' => $e->errors(),
          'error_code' => 422
        ], 422);
      }
    });
  })->create();
