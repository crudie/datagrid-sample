<?php

namespace Main\Repository;

use Main\Model\User;


/**
 * User repository
 */
interface UserRepository
{
    /**
     * Find all users
     *
     * @return User[]
     */
    public function findAll();

    /**
     * Find one user by $Id
     *
     * @param mixed$id
     *
     * @return User|null
     */
    public function findOne($id);

    /**
     * Save transaction
     *
     * @param User $transaction
     */
    public function save(User $transaction);

    /**
     * Delete user
     *
     * @param User $user
     */
    public function delete(User $user);
}