<?php

namespace App\Core\Infra\Repositories;

use App\Core\Data\Repositories\TaskRepository;
use App\Core\Domain\Entities\TaskEntity;
use App\Models\Task;

class EloquentTaskRepository implements TaskRepository
{
    /**
     * Tries to fetch a task that matches the provided id
     *
     * @param int $id
     * @return ?TaskEntity
     */
    public function fetchById(int $id): ?TaskEntity
    {
        /** @var ?Task */
        $task = Task::where('id', $id)
            ->first(['name', 'description', 'status', 'building_id', 'assigned_user_id', 'creator_user_id']);

        if (!$task) {
            return null;
        }

        return new TaskEntity(
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
     * @param TaskEntity $task
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
