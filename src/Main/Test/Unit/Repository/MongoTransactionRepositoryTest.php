<?php

namespace Main\Test\Unit\Repository;

use Main\Model\Transaction;
use Main\Model\User;
use Main\Repository\MongoTransactionRepository;
use Main\Test\Unit\BaseUnitTest;
use Mockery as m;

/**
 * Test MongoTransactionRepository
 */
class MongoTransactionRepositoryTest extends BaseUnitTest
{
    /**
     * Test findAll method when transactions don't exist
     * Method should return empty array
     *
     * @covers MongoTransactionRepository::findAll()
     */
    public function testFindAllWhenTransactionsDoNotExist()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('find')
            ->once()
            ->andReturn([]);
        $mongo->db->transactions = $collection;

        $this->assertEquals(
            [],
            $this->createRepository($mongo)->findAll()
        );
    }

    /**
     * Test findAll method when transactions exist
     * Method should return array of Transaction
     *
     * @covers MongoTransactionRepository::findAll()
     */
    public function testFindAllWhenTransactionsExist()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('find')
            ->once()
            ->andReturn([
                ['_id' => '1', 'user' => ['_id' => '1', 'name' => 'Ivan'], 'sum' => 1000, 'comment' => 'Test'],
                ['_id' => '2', 'user' => ['_id' => '2', 'name' => 'Marian'], 'sum' => -500, 'comment' => 'Test again'],
            ]);
        $mongo->db->transactions = $collection;

        $this->assertEquals(
            [
                new Transaction(1, new User(1, 'Ivan'), 1000, 'Test'),
                new Transaction(2, new User(2, 'Marian'), -500, 'Test again'),
            ],
            $this->createRepository($mongo)->findAll()
        );
    }


    /**
     * Test findOne method when transaction does not exists
     * Method should return null
     *
     * @covers MongoTransactionRepository::findOne()
     */
    public function testFindOneWhenTransactionDoesNotExists()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('findOne')
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')])
            ->once()
            ->andReturn(null);
        $mongo->db->transactions = $collection;

        $this->assertEquals(
            null,
            $this->createRepository($mongo)->findOne('5851386eae57b84b40605c62')
        );
    }

    /**
     * Test findOne method when transaction does exists
     * Method should return instance of Transaction class
     *
     * @covers MongoTransactionRepository::findOne()
     */
    public function testFindOneWhenTransactionExists()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('findOne')
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')])
            ->once()
            ->andReturn(['_id' => '5851386eae57b84b40605c62', 'user' => ['_id' => '1', 'name' => 'Ivan'], 'sum' => 1000, 'comment' => 'Test']);

        $mongo->db->transactions = $collection;

        $this->assertEquals(
            new Transaction('5851386eae57b84b40605c62', new User(1, 'Ivan'), 1000, 'Test'),
            $this->createRepository($mongo)->findOne('5851386eae57b84b40605c62')
        );
    }

    /**
     * Test save method when transaction is new one
     * Method should insert transaction in mongodb
     *
     * @covers MongoTransactionRepository::save()
     */
    public function testSaveWhenTransactionIsNewOne()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $result = $this->createMongoInsertOneResultMock();
        $result->shouldReceive('getInsertedId')
            ->once()
            ->andReturn('123');

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('insertOne')
            ->once()
            ->with([
                'user' => [
                    '_id' => 1,
                    'name' => 'Ivan'
                ],
                'sum' => 500,
                'comment' => 'My job is done'
            ])
            ->andReturn($result);
        $mongo->db->transactions = $collection;

        $transaction = new Transaction(null, new User(1, 'Ivan'), 500, 'My job is done');

        $this->createRepository($mongo)->save($transaction);

        $this->assertEquals('123', $transaction->getId());
    }

    /**
     * Test save method when transaction is exists
     * Method should update transaction in mongodb
     *
     * @covers MongoTransactionRepository::save()
     */
    public function testSaveWhenTransactionIsExists()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('updateOne')
            ->once()
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')], [
                '$set' => [
                    'user' => [
                        '_id' => 1,
                        'name' => 'Ivan'
                    ],
                    'sum' => 600,
                    'comment' => 'My job is already done'
                ]
            ], ['upsert' => true]);
        $mongo->db->transactions = $collection;

        $this->createRepository($mongo)->save(new Transaction('5851386eae57b84b40605c62', new User(1, 'Ivan'), 600, 'My job is already done'));
    }

    /**
     * Test delete method
     *
     * @covers MongoTransactionRepository::delete()
     */
    public function testDelete()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('deleteOne')
            ->once()
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')]);
        $mongo->db->transactions = $collection;

        $this->createRepository($mongo)->delete(new Transaction('5851386eae57b84b40605c62', new User(1, 'Ivan'), 600, 'My job is already done'));
    }

    /**
     * Create repository
     *
     * @param \MongoDB\Client $client
     *
     * @return MongoTransactionRepository
     */
    private function createRepository(\MongoDB\Client $client)
    {
        return new MongoTransactionRepository($client);
    }
}