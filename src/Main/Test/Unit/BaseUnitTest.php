<?php

namespace Main\Test\Unit;

use Mockery as m;

use PHPUnit\Framework\TestCase;


/**
 * Base unit test
 */
abstract class BaseUnitTest extends TestCase
{
    /**
     * Close mockery
     */
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }


    /**
     * Create mongo mock
     *
     * @return m\MockInterface
     */
    protected function createMongoMock()
    {
        $m = m::mock(\MongoDB\Client::class);

        $m->shouldReceive('selectDatabase');

        return $m;
    }

    /**
     *
     * Create mongo database mock
     *
     * @return m\MockInterface
     */
    protected function createMongoDBMock()
    {
        return m::mock(\MongoDB\Database::class);
    }

    /**
     * Create mongo collection mock
     *
     * @return m\MockInterface
     */
    protected function createMongoCollectionMock()
    {
        return m::mock(\MongoDB\Collection::class);
    }

    /**
     * Create mongo insert one result mock
     *
     * @return m\MockInterface
     */
    protected function createMongoInsertOneResultMock()
    {
        return m::mock(\MongoDB\InsertOneResult::class);
    }
}