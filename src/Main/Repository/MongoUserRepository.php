<?php

namespace Main\Repository;

use Main\Model\Transaction;
use Main\Model\User;
use MongoDB\Client;

/**
 * Mongo implementation of UserRepository
 */
class MongoUserRepository implements UserRepository
{
    /**
     * @var Client
     */
    private $connection;

    /**
     * MongoTransactionRepository constructor.
     * @param Client $connection
     */
    public function __construct(Client $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Find all users
     *
     * @return User[]
     */
    public function findAll()
    {
        $data = $this->connection->db->users->find();
        $result = [];

        foreach ($data as $item) {
            $result[] = new User($item['_id'], $item['name']);
        }

        return $result;
    }

    /**
     * Find one user by $Id
     *
     * @param mixed $id
     *
     * @return User|null
     */
    public function findOne($id)
    {
        $item = $this->connection->db->users->findOne(['_id' => new \MongoDB\BSON\ObjectID($id)]);

        if ($item) {
            return new User($item['_id'], $item['name']);
        }

        return null;
    }

    /**
     * Save user
     *
     * @param User $user
     */
    public function save(User $user)
    {
        if ($user->getId()) {
            $this->connection->db->users->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectID($user->getId())], [
                '$set' => [
                    'name' => $user->getName()
                ]
            ], ['upsert' => true]);
        } else {
            $result = $this->connection->db->users->insertOne([
                'name' => $user->getName()
            ]);

            $user->setId($result->getInsertedId());
        }
    }

    /**
     * Delete user
     *
     * @param User $user
     */
    public function delete(User $user)
    {
        $this->connection->db->users->deleteOne(['_id' => new \MongoDB\BSON\ObjectID($user->getId())]);
    }
}