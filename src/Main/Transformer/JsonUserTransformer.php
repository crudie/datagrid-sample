<?php

namespace Main\Transformer;

use Main\Model\User;

/**
 * Transform User to an array
 */
class JsonUserTransformer
{
    /**
     * Transform transaction to an array
     *
     * @param User $user
     *
     * @return array
     */
    public static function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName()
        ];
    }
}