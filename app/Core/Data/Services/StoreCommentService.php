<?php

namespace App\Core\Data\Services;

use App\Core\Data\Repositories\CommentRepository;
use App\Core\Data\Repositories\TaskRepository;
use App\Core\Data\Repositories\UserRepository;
use App\Core\Domain\Entities\CommentEntity;
use App\Core\Domain\Exceptions\CreatorUserNotFoundException;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Exceptions\UnauthorizedToCommentException;
use App\Core\Domain\UseCases\StoreCommentUseCase;

class StoreCommentService implements StoreCommentUseCase
{
    /**
     * StoreCommentService contructor
     *
     * @param UserRepository $userRepository
     * @param TaskRepository $taskRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TaskRepository $taskRepository,
        private readonly CommentRepository $commentRepository
    ) {
    }

    /**
     * Stores a new comment
     *
     * @param array<string, string|int> $payload
     * @throws CreatorUserNotFoundException
     * @throws TaskNotFoundException
     * @throws UnauthorizedToCommentException
     * @return void
     */
    public function storeComment(array $payload): void
    {
        $userWhoWillComment = $this->userRepository->fetchById($payload['creator_user_id']);

        if (!$userWhoWillComment) {
            throw new CreatorUserNotFoundException();
        }

        $taskThatWillBeCommented = $this->taskRepository->fetchById($payload['task_id']);

        if (!$taskThatWillBeCommented) {
            throw new TaskNotFoundException();
        }

        $userAssignedToTheTask = $this->userRepository->fetchById($taskThatWillBeCommented->assignedUserId);

        $canUserComment = $taskThatWillBeCommented->canComment($userWhoWillComment, $userAssignedToTheTask);

        if (!$canUserComment) {
            throw new UnauthorizedToCommentException();
        }

        $commentToStore = new CommentEntity(
            content: $payload['content'],
            taskId: $payload['task_id'],
            creatorUserId: $payload['creator_user_id']
        );

        $this->commentRepository->store($commentToStore);
    }
}
