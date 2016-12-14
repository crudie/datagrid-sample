<?php

namespace Main\Test\Unit\Repository;

use Main\Model\User;
use Main\Repository\MongoUserRepository;
use Main\Test\Unit\BaseUnitTest;
use Mockery as m;

/**
 * Test MongoUserRepositoryTest
 */
class MongoUserRepositoryTest extends BaseUnitTest
{
    /**
     * Test findOne method when user does not exists
     * Method should return null
     *
     * @covers MongoUserRepository::findOne()
     */
    public function testFindOneWhenUserDoesNotExists()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('findOne')
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')])
            ->once()
            ->andReturn(null);
        $mongo->db->users = $collection;

        $this->assertEquals(
            null,
            $this->createRepository($mongo)->findOne('5851386eae57b84b40605c62')
        );
    }

    /**
     * Test findOne method when user does exists
     * Method should return instance of User class
     *
     * @covers MongoUserRepository::findOne()
     */
    public function testFindOneWhenUserExists()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('findOne')
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')])
            ->once()
            ->andReturn(['_id' => '1', 'name' => 'Ivan']);
        $mongo->db->users = $collection;

        $this->assertEquals(
            new User(1, 'Ivan'),
            $this->createRepository($mongo)->findOne('5851386eae57b84b40605c62')
        );
    }

    /**
     * Test findAll method when users don't exist
     * Method should return empty array
     *
     * @covers MongoUserRepository::findAll()
     */
    public function testFindAllWhenUsersDoNotExist()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('find')
            ->once()
            ->andReturn([]);
        $mongo->db->users = $collection;

        $this->assertEquals(
            [],
            $this->createRepository($mongo)->findAll()
        );
    }

    /**
     * Test findAll method when users exist
     * Method should return array of User
     *
     * @covers MongoUserRepository::findAll()
     */
    public function testFindAllWhenUsersExist()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('find')
            ->once()
            ->andReturn([
                ['_id' => '1', 'name' => 'Ivan'],
                ['_id' => '2', 'name' => 'Marian']
            ]);
        $mongo->db->users = $collection;

        $this->assertEquals(
            [
                new User(1, 'Ivan'),
                new User(2, 'Marian')
            ],
            $this->createRepository($mongo)->findAll()
        );
    }

    /**
     * Test save method when a user is a new one
     * Method should insert the user in mongodb
     *
     * @covers MongoUserRepository::save()
     */
    public function testSaveWhenUserIsNewOne()
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
                'name' => 'Ivan'
            ])
            ->andReturn($result);
        $mongo->db->users = $collection;

        $user = new User(null, 'Ivan');

        $this->createRepository($mongo)->save($user);

        $this->assertEquals('123', $user->getId());
    }

    /**
     * Test save method when a user is exists
     * Method should update the user in mongodb
     *
     * @covers MongoUserRepository::save()
     */
    public function testSaveWhenUserIsExists()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('updateOne')
            ->once()
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')], [
                '$set' => [
                    'name' => 'Ivan2'
                ]
            ], ['upsert' => true]);
        $mongo->db->users = $collection;

        $this->createRepository($mongo)->save(new User('5851386eae57b84b40605c62', 'Ivan2'));
    }

    /**
     * Test delete method
     *
     * @covers MongoUserRepository::delete()
     */
    public function testDelete()
    {
        $mongo = $this->createMongoMock();
        $mongo->db = $this->createMongoDBMock();

        $collection = $this->createMongoCollectionMock();
        $collection->shouldReceive('deleteOne')
            ->once()
            ->with(['_id' => new \MongoDB\BSON\ObjectID('5851386eae57b84b40605c62')]);
        $mongo->db->users = $collection;

        $this->createRepository($mongo)->delete(new User('5851386eae57b84b40605c62', 'Ivan'));
    }

    /**
     * Create repository
     *
     * @param \MongoDB\Client $client
     *
     * @return MongoUserRepository
     */
    private function createRepository(\MongoDB\Client $client)
    {
        return new MongoUserRepository($client);
    }
}