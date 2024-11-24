<?php

namespace App\Core\Domain\UseCases;

use App\Core\Domain\Exceptions\UnauthorizedAttachedTeamException;

interface StoreTaskUseCase
{
    /**
     * Stores a new task
     *
     * @param array<string, string|int> $payload
     * @throws UnauthorizedAttachedTeamException
     * @return void
     */
    public function storeTask(array $payload): void;
}
