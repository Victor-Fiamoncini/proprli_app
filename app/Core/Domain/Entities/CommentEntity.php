<?php

namespace App\Core\Domain\Entities;

class CommentEntity
{
    /**
     * CommentEntity contructor
     *
     * @param string $content
     * @param int $taskId
     * @param int $creatorUserId
     */
    public function __construct(
        public readonly string $content,
        public readonly int $taskId,
        public readonly int $creatorUserId
    ) {
    }
}