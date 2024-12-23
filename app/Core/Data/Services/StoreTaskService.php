<?php

namespace App\Core\Data\Services;

use App\Core\Data\Repositories\TaskRepository;
use App\Core\Data\Repositories\UserRepository;
use App\Core\Domain\Builders\TaskEntityBuilder;
use App\Core\Domain\Exceptions\AssignedUserNotFoundException;
use App\Core\Domain\Exceptions\CreatorUserNotFoundException;
use App\Core\Domain\Exceptions\UnauthorizedAttachedTeamException;
use App\Core\Domain\UseCases\StoreTaskUseCase;

class StoreTaskService implements StoreTaskUseCase
{
    /**
     * StoreTaskService contructor
     *
     * @param UserRepository $userRepository
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TaskRepository $taskRepository
    ) {
    }

    /**
     * Stores a new task
     *
     * @param array<string, string|int> $payload
     * @throws CreatorUserNotFoundException
     * @throws AssignedUserNotFoundException
     * @throws UnauthorizedAttachedTeamException
     * @return void
     */
    public function storeTask(array $payload): void
    {
        $creatorUser = $this->userRepository->fetchById($payload['creator_user_id']);

        if (!$creatorUser) {
            throw new CreatorUserNotFoundException();
        }

        $assignedUser = $this->userRepository->fetchById($payload['assigned_user_id']);

        if (!$assignedUser) {
            throw new AssignedUserNotFoundException();
        }

        if ($creatorUser->teamId !== $assignedUser->teamId) {
            throw new UnauthorizedAttachedTeamException();
        }

        $taskEntityBuilder = new TaskEntityBuilder();
        $taskToStore = $taskEntityBuilder->setName($payload['name'])
            ->setDescription($payload['description'])
            ->setStatus($payload['status'])
            ->setBuildingId($payload['building_id'])
            ->setAssignedUserId($payload['assigned_user_id'])
            ->setCreatorUserId($payload['creator_user_id'])
            ->build();

        $this->taskRepository->store($taskToStore);
    }
}
