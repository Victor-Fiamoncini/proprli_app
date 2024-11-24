<?php

namespace App\Http\Controllers;

use App\Core\Domain\UseCases\StoreTaskUseCase;
use App\Http\Requests\TaskStoreRequest;
use App\Models\Building;
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
      * Stores a task to the requested building
      *
      * @param Building
      * @param TaskStoreRequest $request
      * @return Response
      */
    public function store(Building $building, TaskStoreRequest $request): Response
    {
        $payload = $request->safe()->only([
            'name',
            'description',
            'status',
            'assigned_user_id',
            'creator_user_id',
        ]);
        $payload['building_id'] = $building->id;

        $this->storeTaskUseCase->storeTask($payload);

        return response()->noContent(Response::HTTP_CREATED);

    }
}
