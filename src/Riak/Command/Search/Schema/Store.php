<?php

/*
Copyright 2014 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Widda\Riak\Command\Search\Schema;

use Widda\Riak\Command;
use Widda\Riak\CommandInterface;

/**
 * Class Store
 *
 * Riak Yokozuna Search Schema Store
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'PUT';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $schema = '';

    /**
     * @var Command\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\Search\StoreSchema $builder)
    {
        parent::__construct($builder);

        $this->name = $builder->getName();
        $this->schema = $builder->getSchema();
    }

    public function getEncodedData()
    {
        return $this->getData();
    }

    public function getData()
    {
        return $this->schema;
    }

    /**
     * @param $statusCode
     * @param array $responseHeaders
     * @param string $responseBody
     *
     * @return $this
     */
    public function setResponse($statusCode, $responseHeaders = [], $responseBody = '')
    {
        $this->response = new Command\Response($statusCode, $responseHeaders, $responseBody);

        return $this;
    }

    /**
     * @return Command\Response
     */
    public function execute()
    {
        return parent::execute();
    }

    public function __toString()
    {
        return $this->name;
    }
}