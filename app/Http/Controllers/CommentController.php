<?php

namespace App\Http\Controllers;

use App\Core\Domain\UseCases\StoreCommentUseCase;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Task;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * CommentController contructor
     *
     * @param StoreCommentUseCase $storeCommentUseCase
     */
    public function __construct(
        protected readonly StoreCommentUseCase $storeCommentUseCase,
    ) {
    }

    /**
     * Stores a comment to the requested task
     *
     * @param Task $task
     * @param CommentStoreRequest $request
     * @return Response
     */
    public function store(Task $task, CommentStoreRequest $request): Response
    {
        $payload = $request->safe()->only(['content', 'creator_user_id']);
        $payload['task_id'] = $task->id;

        $this->storeCommentUseCase->storeComment($payload);

        return response()->noContent(Response::HTTP_CREATED);
    }
}
