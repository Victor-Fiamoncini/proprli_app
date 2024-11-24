<?php

namespace App\Core\Domain\Entities;

use App\Core\Domain\Exceptions\InvalidTaskStatusException;

enum TaskStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case REJECTED = 'REJECTED';
}

class TaskEntity
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $status,
        public readonly int $buildingId,
        public readonly int $assignedUserId,
        public readonly int $creatorUserId
    ) {
        if (!TaskStatus::tryFrom($this->status)) {
            throw new InvalidTaskStatusException();
        }
    }
}
