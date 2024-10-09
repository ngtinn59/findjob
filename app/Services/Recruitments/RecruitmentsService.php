<?php

namespace App\Services\Recruitments;

use App\Repositories\Recruitments\RecruitmentsRepositoryInterface;
use App\Services\BaseService;

class RecruitmentsService extends BaseService implements RecruitmentsServiceInterface
{
    public $repository;

    public function __construct(RecruitmentsRepositoryInterface $RecruitmentsRepository) {
        $this->repository = $RecruitmentsRepository;
    }
}
