<?php

namespace App\Core\Data\Repositories;

use App\Core\Domain\Entities\TaskEntity;

interface TaskRepository
{
    /**
     * Fetch tasks along with their comments (based on provided filters)
     *
     * @param array<string, string|int> $filters
     * @return TaskEntity[]
     */
    public function fetch(array $filters): array;

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
     * @param TaskEntity $taskEntity
     * @return void
     */
    public function store(TaskEntity $taskEntity): void;
}
