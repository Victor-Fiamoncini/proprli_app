<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected readonly Building $building;
    protected readonly Task $task;
    protected readonly User $creatorUser;
    protected readonly User $assignedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $team = Team::factory()->has(User::factory()->count(2), 'members')->create();

        $this->building = Building::factory()->create();

        $this->creatorUser = $team->members->first();
        $this->assignedUser = $team->members->last();

        $this->task = Task::factory()->create([
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']),
            'building_id' => $this->building->id,
            'creator_user_id' => $this->creatorUser->id,
            'assigned_user_id' => $this->assignedUser->id,
        ]);
    }

    public function test_should_store_a_new_comment(): void
    {
        $content = $this->faker->sentence(2);

        $response = $this->post(
            uri: route('tasks.comments.store', $this->task->id),
            data: ['content' => $content, 'creator_user_id' => $this->creatorUser->id]
        );

        $response->assertCreated();

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_should_get_422_after_trying_to_store_a_comment_with_an_unknown_creator(): void
    {
        $content = $this->faker->sentence(2);

        $response = $this->post(
            uri: route('tasks.comments.store', $this->task->id),
            data: ['content' => $content, 'creator_user_id' => 9999],
            headers: ['Accept' => 'application/json']
        );

        $response->assertUnprocessable();

        $this->assertDatabaseEmpty('comments');
    }

    public function test_should_get_422_after_trying_to_store_a_comment_in_an_unknown_task(): void
    {
        $content = $this->faker->sentence(2);

        $response = $this->post(
            uri: route('tasks.comments.store', 9999),
            data: ['content' => $content, 'creator_user_id' => $this->creatorUser->id],
            headers: ['Accept' => 'application/json']
        );

        $response->assertNotFound();

        $this->assertDatabaseEmpty('comments');
    }

    public function test_should_get_401_after_trying_to_store_a_comment_with_a_different_team_member(): void
    {
        $otherTeam = Team::factory()->has(User::factory()->count(2), 'members')->create();
        $otherTask = Task::factory()->create([
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']),
            'building_id' => $this->building->id,
            'creator_user_id' => $otherTeam->members->first()->id,
            'assigned_user_id' => $otherTeam->members->last()->id,
        ]);

        $content = $this->faker->sentence(2);

        $response = $this->post(
            uri: route('tasks.comments.store', $otherTask->id),
            data: ['content' => $content, 'creator_user_id' => $this->creatorUser->id],
            headers: ['Accept' => 'application/json']
        );

        $response->assertUnauthorized();

        $this->assertDatabaseEmpty('comments');
    }
}
