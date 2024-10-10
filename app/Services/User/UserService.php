<?php

namespace App\Services\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Services\BaseService;
use App\Services\Product\ProductServiceInterface;

class UserService extends BaseService implements UserServiceInterface
{
    public $repository;

    public function __construct(UserRepositoryInterface $UserRepository) {
        $this->repository = $UserRepository;
    }
}
