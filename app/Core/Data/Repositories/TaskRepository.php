<?php

namespace App\Core\Data\Repositories;

use App\Core\Domain\Entities\TaskEntity;

interface TaskRepository
{
    /**
     * Tries to fetch a task that matches the provided id
     *
     * @param int $id
     * @return ?TaskEntity
     */
    public function fetchById(int $id): ?TaskEntity;

    /**
     * Stores a new task
     *
     * @param TaskEntity $task
     * @return void
     */
    public function store(TaskEntity $taskEntity): void;
}
