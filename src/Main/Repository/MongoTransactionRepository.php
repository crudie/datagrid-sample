<?php

namespace Main\Repository;

use Main\Model\Transaction;
use Main\Model\User;
use MongoDB\Client;

/**
 * Mongo implementation of TransactionRepository
 */
class MongoTransactionRepository implements TransactionRepository
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
     * Find all transactions
     *
     * @return Transaction[]
     */
    public function findAll()
    {
        $data = $this->connection->db->transactions->find([], ['sort' => ['_id' => -1]]);
        $result = [];

        foreach ($data as $item) {
            $result[] = new Transaction(
                $item['_id'],
                new User($item['user']['_id'], $item['user']['name']),
                $item['sum'],
                $item['comment']
            );
        }

        return $result;
    }

    /**
     * Find one user by $Id
     *
     * @param mixed $id
     *
     * @return Transaction|null
     */
    public function findOne($id)
    {
        $item = $this->connection->db->transactions->findOne(['_id' => new \MongoDB\BSON\ObjectID($id)]);

        if ($item) {
            return new Transaction(
                $item['_id'],
                new User($item['user']['_id'], $item['user']['name']),
                $item['sum'],
                $item['comment']
            );
        }

        return null;
    }

    /**
     * Save transaction
     *
     * @param Transaction $transaction
     */
    public function save(Transaction $transaction)
    {
        if ($transaction->getId()) {
            $this->connection->db->transactions->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectID($transaction->getId())], [
                '$set' => [
                    'user' => [
                        '_id' => $transaction->getUser()->getId(),
                        'name' => $transaction->getUser()->getName()
                    ],
                    'sum' => $transaction->getSum(),
                    'comment' => $transaction->getComment()
                ]], ['upsert' => true]);
        } else {
            $result = $this->connection->db->transactions->insertOne([
                'user' => [
                    '_id' => $transaction->getUser()->getId(),
                    'name' => $transaction->getUser()->getName()
                ],
                'sum' => $transaction->getSum(),
                'comment' => $transaction->getComment()
            ]);

            $transaction->setId($result->getInsertedId());
        }
    }

    /**
     * Delete transaction
     *
     * @param Transaction $transaction
     */
    public function delete(Transaction $transaction)
    {
        $this->connection->db->transactions->deleteOne(['_id' => new \MongoDB\BSON\ObjectID($transaction->getId())]);
    }
}