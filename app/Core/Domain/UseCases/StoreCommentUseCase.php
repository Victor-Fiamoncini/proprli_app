<?php

namespace App\Core\Domain\UseCases;

use App\Core\Domain\Exceptions\CreatorUserNotFoundException;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Exceptions\UnauthorizedToCommentException;

interface StoreCommentUseCase
{
    /**
     * Stores a new comment
     *
     * @param array<string, string|int> $payload
     * @throws CreatorUserNotFoundException
     * @throws TaskNotFoundException
     * @throws UnauthorizedToCommentException
     * @return void
     */
    public function storeComment(array $payload): void;
}
