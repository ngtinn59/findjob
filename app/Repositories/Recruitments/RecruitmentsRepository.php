<?php

namespace App\Repositories\Recruitments;

use App\Models\ProductComment;
use App\Models\recruitments;
use App\Repositories\BaseRepository;

class RecruitmentsRepository extends BaseRepository implements RecruitmentsRepositoryInterface
{

    public function getModel()
    {
        return recruitments::class;
    }
}
