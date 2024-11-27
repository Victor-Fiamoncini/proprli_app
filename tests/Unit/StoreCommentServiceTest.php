<?php

namespace Tests\Unit;

use App\Core\Data\Repositories\CommentRepository;
use App\Core\Data\Repositories\TaskRepository;
use App\Core\Data\Repositories\UserRepository;
use App\Core\Data\Services\StoreCommentService;
use App\Core\Domain\Entities\CommentEntity;
use App\Core\Domain\Entities\TaskEntity;
use App\Core\Domain\Entities\UserEntity;
use App\Core\Domain\Exceptions\CreatorUserNotFoundException;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Exceptions\UnauthorizedToCommentException;
use App\Core\Domain\UseCases\StoreCommentUseCase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class StoreCommentServiceTest extends TestCase
{
    use WithFaker;

    private readonly MockObject $userRepositoryMock;
    private readonly MockObject $taskRepositoryMock;
    private readonly MockObject $commentRepositoryMock;
    private readonly StoreCommentUseCase $storeCommentUseCase;

    protected function setUp(): void
    {
        $this->setUpFaker();

        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->taskRepositoryMock = $this->createMock(TaskRepository::class);
        $this->commentRepositoryMock = $this->createMock(CommentRepository::class);

        $this->storeCommentUseCase = new StoreCommentService(
            userRepository: (object) $this->userRepositoryMock,
            taskRepository: (object) $this->taskRepositoryMock,
            commentRepository: (object) $this->commentRepositoryMock
        );
    }

    public function test_should_store_a_new_comment_with_provided_params(): void
    {
        $payload = [
            'creator_user_id' => $this->faker()->randomNumber(2),
            'task_id' => $this->faker()->randomNumber(2),
            'content' => $this->faker()->paragraph(2),
        ];

        $fakeTeamId = $this->faker()->randomNumber(2);

        $fakeTaskThatWillBeCommented = new TaskEntity(
            id: $this->faker()->randomNumber(2),
            name: $this->faker()->sentence(2),
            description: $this->faker()->paragraph(4),
            status: $this->faker()->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']),
            buildingId: $this->faker()->randomNumber(2),
            assignedUserId: $this->faker()->randomNumber(2),
            creatorUserId: $this->faker()->randomNumber(2)
        );

        $fakeUserWhoWillComment = new UserEntity($payload['creator_user_id'], $fakeTeamId);
        $fakeUserAssignedToTheTask = new UserEntity($fakeTaskThatWillBeCommented->assignedUserId, $fakeTeamId);

        $this->userRepositoryMock->expects($this->exactly(2))
            ->method('fetchById')
            ->willReturnMap([
                [$payload['creator_user_id'], $fakeUserWhoWillComment],
                [$fakeTaskThatWillBeCommented->assignedUserId, $fakeUserAssignedToTheTask],
            ]);

        $this->taskRepositoryMock->expects($this->once())
            ->method('fetchById')
            ->willReturn($fakeTaskThatWillBeCommented);

        $this->commentRepositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(function (CommentEntity $comment) use ($payload) {
                return $comment instanceof CommentEntity &&
                    $comment->creatorUserId === $payload['creator_user_id'] &&
                    $comment->taskId === $payload['task_id'] &&
                    $comment->content === $payload['content'];
            }));

        $this->storeCommentUseCase->storeComment($payload);
    }

    public function test_should_throw_when_the_creator_user_is_not_found(): void
    {
        $payload = [
            'creator_user_id' => $this->faker()->randomNumber(2),
            'task_id' => $this->faker()->randomNumber(2),
            'content' => $this->faker()->paragraph(2),
        ];

        $this->userRepositoryMock->expects($this->once())
            ->method('fetchById')
            ->willReturn(null);

        $this->expectException(CreatorUserNotFoundException::class);

        $this->storeCommentUseCase->storeComment($payload);
    }

    public function test_should_throw_when_the_task_is_not_found(): void
    {
        $payload = [
            'creator_user_id' => $this->faker()->randomNumber(2),
            'task_id' => $this->faker()->randomNumber(2),
            'content' => $this->faker()->paragraph(2),
        ];

        $fakeUserWhoWillComment = new UserEntity(
            $payload['creator_user_id'],
            $this->faker()->randomNumber(2)
        );

        $this->userRepositoryMock->expects($this->once())
            ->method('fetchById')
            ->willReturn($fakeUserWhoWillComment);

        $this->taskRepositoryMock->expects($this->once())
            ->method('fetchById')
            ->willReturn(null);

        $this->expectException(TaskNotFoundException::class);

        $this->storeCommentUseCase->storeComment($payload);
    }

    public function test_should_throw_if_user_cant_comment_the_task(): void
    {
        $payload = [
            'creator_user_id' => $this->faker()->randomNumber(2),
            'task_id' => $this->faker()->randomNumber(2),
            'content' => $this->faker()->paragraph(2),
        ];

        $fakeTaskThatWillBeCommented = new TaskEntity(
            id: $this->faker()->randomNumber(2),
            name: $this->faker()->sentence(2),
            description: $this->faker()->paragraph(4),
            status: $this->faker()->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']),
            buildingId: $this->faker()->randomNumber(2),
            assignedUserId: $this->faker()->randomNumber(2),
            creatorUserId: $this->faker()->randomNumber(2)
        );

        $fakeUserWhoWillCommentTeamId = $this->faker()->randomNumber(1);
        $fakeUserAssignedToTheTaskTeamId = $this->faker()->randomNumber(2);

        $fakeUserWhoWillComment = new UserEntity(
            $payload['creator_user_id'],
            $fakeUserWhoWillCommentTeamId
        );
        $fakeUserAssignedToTheTask = new UserEntity(
            $fakeTaskThatWillBeCommented->assignedUserId,
            $fakeUserAssignedToTheTaskTeamId
        );

        $this->userRepositoryMock->expects($this->exactly(2))
            ->method('fetchById')
            ->willReturnMap([
                [$payload['creator_user_id'], $fakeUserWhoWillComment],
                [$fakeTaskThatWillBeCommented->assignedUserId, $fakeUserAssignedToTheTask],
            ]);

        $this->taskRepositoryMock->expects($this->once())
            ->method('fetchById')
            ->willReturn($fakeTaskThatWillBeCommented);

        $this->expectException(UnauthorizedToCommentException::class);

        $this->storeCommentUseCase->storeComment($payload);
    }
}
