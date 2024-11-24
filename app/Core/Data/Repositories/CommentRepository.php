<?php

namespace App\Core\Data\Repositories;

use App\Core\Domain\Entities\CommentEntity;

interface CommentRepository
{
    /**
     * Stores a new comment
     *
     * @param CommentEntity $commentEntity
     * @return void
     */
    public function store(CommentEntity $commentEntity): void;
}
