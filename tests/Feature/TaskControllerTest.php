<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected readonly Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = Building::factory()->create();
    }

    public function test_should_fetch_tasks(): void
    {
        $team = Team::factory()->has(User::factory()->count(2), 'members')->create();
        $creatorUser = $team->members->first();
        $assignedUser = $team->members->last();

        Task::factory()->createMany([
            [
                'name' => 'any_name_01',
                'description' => 'any_description_01',
                'status' => 'OPEN',
                'building_id' => $this->building->id,
                'creator_user_id' => $creatorUser->id,
                'assigned_user_id' => $assignedUser->id,
            ],
            [
                'name' => 'any_name_02',
                'description' => 'any_description_02',
                'status' => 'COMPLETED',
                'building_id' => $this->building->id,
                'creator_user_id' => $creatorUser->id,
                'assigned_user_id' => $assignedUser->id,
            ],
            [
                'name' => 'any_name_03',
                'description' => 'any_description_03',
                'status' => 'OPEN',
                'building_id' => $this->building->id,
                'creator_user_id' => $creatorUser->id,
                'assigned_user_id' => $assignedUser->id,
            ],
        ]);

        $queryParams = ['status' => 'OPEN', 'assigned_user_id' => $assignedUser->id];
        $response = $this->getJson(route('tasks.index', $queryParams));

        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'status',
                    'building_id',
                    'creator_user_id',
                    'assigned_user_id',
                    'comments',
                ],
            ],
        ]);
        $response->assertJsonFragment(['name' => 'any_name_01', 'status' => 'OPEN']);
        $response->assertJsonFragment(['name' => 'any_name_03', 'status' => 'OPEN']);
    }

    public function test_should_store_a_new_task(): void
    {
        $team = Team::factory()->has(User::factory()->count(2), 'members')->create();
        $creatorUser = $team->members->first();
        $assignedUser = $team->members->last();

        $name = $this->faker->sentence(1);
        $description = $this->faker->sentence(2);
        $status = $this->faker->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']);

        $response = $this->post(
            uri: route('buildings.tasks.store', $this->building->id),
            data: [
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'building_id' => $this->building->id,
                'creator_user_id' => $creatorUser->id,
                'assigned_user_id' => $assignedUser->id,
            ]
        );

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'building_id' => $this->building->id,
            'creator_user_id' => $creatorUser->id,
            'assigned_user_id' => $assignedUser->id,
        ]);
    }

    public function test_should_get_422_after_trying_to_store_a_task_with_a_unknown_creator(): void
    {
        $team = Team::factory()->has(User::factory()->count(1), 'members')->create();
        $assignedUser = $team->members->last();

        $name = $this->faker->sentence(1);
        $description = $this->faker->sentence(2);
        $status = $this->faker->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']);

        $response = $this->post(
            uri: route('buildings.tasks.store', $this->building->id),
            data: [
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'building_id' => $this->building->id,
                'creator_user_id' => 9999,
                'assigned_user_id' => $assignedUser->id,
            ],
            headers: ['Accept' => 'application/json']
        );

        $response->assertUnprocessable();

        $this->assertDatabaseEmpty('tasks');
    }

    public function test_should_get_422_after_trying_to_store_a_task_with_a_unknown_assign(): void
    {
        $team = Team::factory()->has(User::factory()->count(2), 'members')->create();
        $creatorUser = $team->members->last();

        $name = $this->faker->sentence(1);
        $description = $this->faker->sentence(2);
        $status = $this->faker->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']);

        $response = $this->post(
            uri: route('buildings.tasks.store', $this->building->id),
            data: [
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'building_id' => $this->building->id,
                'creator_user_id' => $creatorUser->id,
                'assigned_user_id' => 9999,
            ],
            headers: ['Accept' => 'application/json']
        );

        $response->assertUnprocessable();

        $this->assertDatabaseEmpty('tasks');
    }

    public function test_should_get_401_after_trying_to_store_a_task_with_different_team_members(): void
    {
        $teams = Team::factory()
            ->has(User::factory()->count(2), 'members')
            ->count(2)
            ->create();
        $creatorUser = $teams->first()->members->first();
        $assignedUser = $teams->last()->members->first();

        $name = $this->faker->sentence(1);
        $description = $this->faker->sentence(2);
        $status = $this->faker->randomElement(['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED']);

        $response = $this->post(
            uri: route('buildings.tasks.store', $this->building->id),
            data: [
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'building_id' => $this->building->id,
                'creator_user_id' => $creatorUser->id,
                'assigned_user_id' => $assignedUser->id,
            ],
            headers: ['Accept' => 'application/json']
        );

        $response->assertUnauthorized();

        $this->assertDatabaseEmpty('tasks');
    }
}
