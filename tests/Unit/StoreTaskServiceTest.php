<?php

namespace Tests\Unit;

use App\Core\Data\Repositories\TaskRepository;
use App\Core\Data\Repositories\UserRepository;
use App\Core\Data\Services\StoreTaskService;
use App\Core\Domain\Builders\TaskEntityBuilder;
use App\Core\Domain\Entities\TaskEntity;
use App\Core\Domain\Entities\UserEntity;
use App\Core\Domain\UseCases\StoreTaskUseCase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class StoreTaskServiceTest extends TestCase
{
    use WithFaker;

    private readonly MockObject $userRepositoryMock;
    private readonly MockObject $taskRepositoryMock;
    private readonly MockObject $taskEntityBuilderMock;
    private readonly StoreTaskUseCase $storeTaskService;

    protected function setUp(): void
    {
        $this->setUpFaker();

        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->taskRepositoryMock = $this->createMock(TaskRepository::class);
        $this->taskEntityBuilderMock = $this->createMock(TaskEntityBuilder::class);

        $this->storeTaskService = new StoreTaskService(
            (object) $this->userRepositoryMock,
            (object) $this->taskRepositoryMock
        );
    }

    public function test_should_store_a_new_task_with_provided_params(): void
    {
        $payload = [
            'name' => $this->faker()->sentence(2),
            'description' => $this->faker()->paragraph(4),
            'status' => $this->faker()->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']),
            'building_id' => $this->faker()->randomNumber(2),
            'creator_user_id' => $this->faker()->randomNumber(2),
            'assigned_user_id' => $this->faker()->randomNumber(2),
        ];

        $fakeTeamId = $this->faker()->randomNumber(2);
        $fakeCreatorUser = new UserEntity($payload['creator_user_id'], $fakeTeamId);
        $fakeAssignedUser = new UserEntity($payload['assigned_user_id'], $fakeTeamId);

        $this->userRepositoryMock->expects($this->exactly(2))
            ->method('fetchById')
            ->willReturnMap([
                [$payload['creator_user_id'], $fakeCreatorUser],
                [$payload['assigned_user_id'], $fakeAssignedUser],
            ]);

        $this->taskRepositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(function (TaskEntity $task) use ($payload) {
                return $task instanceof TaskEntity &&
                    $task->name === $payload['name'] &&
                    $task->description === $payload['description'] &&
                    $task->status === $payload['status'] &&
                    $task->buildingId === $payload['building_id'] &&
                    $task->assignedUserId === $payload['assigned_user_id'] &&
                    $task->creatorUserId === $payload['creator_user_id'];
            }));

        $this->storeTaskService->storeTask($payload);
    }
}
