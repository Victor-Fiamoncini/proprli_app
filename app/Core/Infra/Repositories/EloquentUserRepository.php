<?php

namespace App\Core\Infra\Repositories;

use App\Core\Data\Repositories\UserRepository;
use App\Core\Domain\Entities\UserEntity;
use App\Models\User;

class EloquentUserRepository implements UserRepository
{
    /**
     * Tries to fetch an user that matches the provided id
     *
     * @param int $id
     * @return ?UserEntity
     */
    public function fetchById(int $id): ?UserEntity
    {
        /** @var ?User */
        $user = User::where('id', $id)->first(['id', 'team_id']);

        if (!$user) {
            return null;
        }

        return new UserEntity(id: $user->id, teamId:$user->team_id);
    }

}
