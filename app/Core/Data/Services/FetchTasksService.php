<?php

namespace App\Core\Data\Services;

use App\Core\Data\Repositories\TaskRepository;
use App\Core\Domain\Entities\TaskEntity;
use App\Core\Domain\UseCases\FetchTasksUseCase;

class FetchTasksService implements FetchTasksUseCase
{
    /**
     * FetchTasksService contructor
     *
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {
    }

    /**
     * Fetch tasks along with their comments (based on provided filters)
     *
     * @param array<string, string|int> $filters
     * @return TaskEntity[]
     */
    public function fetchTasks(array $filters): array
    {
        return $this->taskRepository->fetch($filters);
    }
}
