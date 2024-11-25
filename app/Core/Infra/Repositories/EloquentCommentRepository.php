<?php

namespace App\Core\Infra\Repositories;

use App\Core\Data\Repositories\CommentRepository;
use App\Core\Domain\Entities\CommentEntity;
use App\Models\Comment;

class EloquentCommentRepository implements CommentRepository
{
    /**
     * Stores a new comment
     *
     * @param CommentEntity $commentEntity
     * @return void
     */
    public function store(CommentEntity $commentEntity): void
    {
        Comment::create([
            'content' => $commentEntity->content,
            'task_id' => $commentEntity->taskId,
            'creator_user_id' => $commentEntity->creatorUserId,
        ]);
    }
}
