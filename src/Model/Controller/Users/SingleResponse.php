<?php

namespace App\Model\Controller\Users;

use App\Model\Entity\Local\User;
use Hippy\Model\Model;

/**
 * @method User getUser()
 * @method SingleResponse setUser(User $value)
 */
class SingleResponse extends Model
{
    /**
     * @param User $user
     */
    public function __construct(protected User $user)
    {
        parent::__construct();
    }
}
