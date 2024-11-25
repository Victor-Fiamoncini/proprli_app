<?php

namespace App\Exceptions;

use App\Core\Domain\Exceptions\AssignedUserNotFoundException;
use App\Core\Domain\Exceptions\CreatorUserNotFoundException;
use App\Core\Domain\Exceptions\InvalidTaskStatusException;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Exceptions\UnauthorizedAttachedTeamException;
use App\Core\Domain\Exceptions\UnauthorizedToCommentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());

            return response()->json([
                'error' => 'Resource not found',
                'message' => "The requested $model was not found with provided params",
            ], Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof InvalidTaskStatusException) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if (
            $e instanceof AssignedUserNotFoundException ||
            $e instanceof CreatorUserNotFoundException ||
            $e instanceof TaskNotFoundException
        ) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        if (
            $e instanceof UnauthorizedAttachedTeamException ||
            $e instanceof UnauthorizedToCommentException
        ) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return parent::render($request, $e);
    }
}
