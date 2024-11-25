<?php

namespace App\Core\Domain\UseCases;

use App\Core\Domain\Entities\TaskEntity;

interface FetchTasksUseCase
{
    /**
     * Fetch tasks along with their comments (based on provided filters)
     *
     * @param array<string, string|int> $filters
     * @return TaskEntity[]
     */
    public function fetchTasks(array $filters): array;
}
