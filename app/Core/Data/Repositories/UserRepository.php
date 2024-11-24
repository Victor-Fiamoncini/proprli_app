<?php

namespace App\Core\Data\Repositories;

use App\Core\Domain\Entities\UserEntity;

interface UserRepository
{
    /**
     * Tries to fetch an user that matches the provided id
     *
     * @param int $id
     * @return ?UserEntity
     */
    public function fetchById(int $id): ?UserEntity;
}
