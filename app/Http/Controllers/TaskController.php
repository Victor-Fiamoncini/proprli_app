<?php

namespace App\Http\Controllers;

use App\Core\Domain\UseCases\StoreTaskUseCase;
use App\Http\Requests\TaskStoreRequest;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * TaskController contructor
     *
     * @param StoreTaskUseCase $storeTaskUseCase
     */
    public function __construct(
        protected readonly StoreTaskUseCase $storeTaskUseCase,
    ) {
    }

    /**
      * Stores a task
      *
      * @param TaskStoreRequest $request
      * @return Response
      */
    public function store(TaskStoreRequest $request): Response
    {
        $payload = $request->safe()->only([
            'name',
            'description',
            'status',
            'building_id',
            'assigned_user_id',
            'creator_user_id',
        ]);

        $this->storeTaskUseCase->storeTask($payload);

        return response()->noContent(Response::HTTP_CREATED);

    }
}
