<?php

/*
Copyright 2015 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class StoreObjectTest extends TestCase
{
    /**
     * Test command builder construct
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreWithKey($riak)
    {
        // build an object
        $builder = new Command\Builder\StoreObject($riak);
        $builder->buildObject('some_data');
        $builder->buildLocation('some_key', 'some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\Object\Store', $command);
        $this->assertInstanceOf('Basho\Riak\Object', $command->getObject());
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertInstanceOf('Basho\Riak\Location', $command->getLocation());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('default', $command->getBucket()->getType());
        $this->assertEquals('some_key', $command->getLocation()->getKey());
    }

    /**
     * Test command builder construct
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreWithOutKey($riak)
    {
        // build an object
        $builder = new Command\Builder\StoreObject($riak);
        $builder->buildObject('some_data');
        $builder->buildBucket('some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\Object\Store', $command);
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
    }

    /**
     * Tests validate properly verifies the Object is not there
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateObject($riak)
    {
        $builder = new Command\Builder\StoreObject($riak);
        $builder->buildBucket('some_bucket');
        $command = $builder->build();
    }

    /**
     * Tests validate properly verifies the Bucket is not there
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateBucket($riak)
    {
        $builder = new Command\Builder\StoreObject($riak);
        $builder->buildObject('some_data');
        $builder->build();
    }

    /**
     * Tests that attempting to store an object generates headers for any
     * 2i entries on the object.
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreObjectWithIndexGeneratesHeaders($riak)
    {
        $inputHeaders = ['My-Header' => 'cats', 'x-riak-index-foo_bin' => 'bar, baz', 'x-riak-index-foo_int' => '42, 50'];
        $builder = new Command\Builder\StoreObject($riak);
        $builder->buildObject('some_data', $inputHeaders);
        $builder->buildBucket('some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\Object\Store', $command);
        $headers = $command->getHeaders();

        $this->assertNotNull($headers);
        $this->assertEquals(5, count($headers));
        $this->assertEquals(['My-Header', 'cats'], $headers[0]);
        $this->assertEquals(['x-riak-index-foo_bin', 'bar'], $headers[1]);
        $this->assertEquals(['x-riak-index-foo_bin', 'baz'], $headers[2]);
        $this->assertEquals(['x-riak-index-foo_int', '42'], $headers[3]);
        $this->assertEquals(['x-riak-index-foo_int', '50'], $headers[4]);

    }
}