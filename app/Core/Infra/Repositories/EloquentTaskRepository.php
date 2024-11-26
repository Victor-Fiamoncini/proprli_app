<?php

namespace App\Core\Infra\Repositories;

use App\Core\Data\Repositories\TaskRepository;
use App\Core\Domain\Builders\CommentEntityBuilder;
use App\Core\Domain\Entities\TaskEntity;
use App\Models\Comment;
use App\Models\Task;

class EloquentTaskRepository implements TaskRepository
{
    /**
     * Fetch tasks along with their comments (based on provided filters)
     *
     * @param array<string, string|int> $filters
     * @return TaskEntity[]
     */
    public function fetch(array $filters): array
    {
        $query = Task::with(['comments:id,content,task_id,creator_user_id'])->select(
            'id',
            'name',
            'description',
            'status',
            'building_id',
            'assigned_user_id',
            'creator_user_id'
        );

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_user_id'])) {
            $query->where('assigned_user_id', $filters['assigned_user_id']);
        }

        if (isset($filters['building_id'])) {
            $query->where('building_id', $filters['building_id']);
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        return $query->get()->map(function (Task $task) {
            return new TaskEntity(
                id: $task->id,
                name: $task->name,
                description: $task->description,
                status: $task->status,
                buildingId: $task->building_id,
                assignedUserId: $task->assigned_user_id,
                creatorUserId: $task->creator_user_id,
                comments: $task->comments->map(function (Comment $comment) {
                    $commentEntityBuilder = new CommentEntityBuilder();

                    return $commentEntityBuilder->setId($comment->id)
                        ->setContent($comment->content)
                        ->setCreatorUserId($comment->creator_user_id)
                        ->build();
                })->toArray()
            );
        })->toArray();
    }

    /**
     * Tries to fetch a task that matches the provided id
     *
     * @param int $id
     * @return ?TaskEntity
     */
    public function fetchById(int $id): ?TaskEntity
    {
        /** @var ?Task */
        $task = Task::where('id', $id)->first([
            'id',
            'name',
            'description',
            'status',
            'building_id',
            'assigned_user_id',
            'creator_user_id',
        ]);

        if (!$task) {
            return null;
        }

        return new TaskEntity(
            id: $task->id,
            name: $task->name,
            description: $task->description,
            status: $task->status,
            buildingId: $task->building_id,
            assignedUserId: $task->assigned_user_id,
            creatorUserId: $task->creator_user_id
        );
    }

    /**
     * Stores a new task
     *
     * @param TaskEntity $taskEntity
     * @return void
     */
    public function store(TaskEntity $taskEntity): void
    {
        Task::create([
            'name' => $taskEntity->name,
            'description' => $taskEntity->description,
            'status' => $taskEntity->status,
            'building_id' => $taskEntity->buildingId,
            'assigned_user_id' => $taskEntity->assignedUserId,
            'creator_user_id' => $taskEntity->creatorUserId,
        ]);
    }
}
