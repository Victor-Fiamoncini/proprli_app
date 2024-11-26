<?php

namespace App\Http\Controllers;

use App\Core\Domain\UseCases\FetchTasksUseCase;
use App\Core\Domain\UseCases\StoreTaskUseCase;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Resources\TaskCollection;
use App\Models\Building;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * TaskController contructor
     *
     * @param FetchTasksUseCase $fetchTasksUseCase
     * @param StoreTaskUseCase $storeTaskUseCase
     */
    public function __construct(
        protected readonly FetchTasksUseCase $fetchTasksUseCase,
        protected readonly StoreTaskUseCase $storeTaskUseCase,
    ) {
    }

    /**
     * Gets the filtered tasks along with their comments
     *
     * @param TaskIndexRequest $request
     * @return TaskCollection
     */
    public function index(TaskIndexRequest $request): TaskCollection
    {
        $filters = $request->validated();

        $fetchedTasks = $this->fetchTasksUseCase->fetchTasks($filters);

        return new TaskCollection(collect($fetchedTasks));
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
