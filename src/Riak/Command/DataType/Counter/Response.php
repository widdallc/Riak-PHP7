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

namespace Widda\Riak\Command\DataType\Counter;

use Widda\Riak\DataType\Counter;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Widda\Riak\Command\Response
{
    /**
     * @var \Widda\Riak\DataType\Counter|null
     */
    protected $counter = NULL;

    public function __construct($statusCode, $headers = [], $body = '')
    {
        parent::__construct($statusCode, $headers, $body);

        // make sure body isn't only whitespace & has a value for the counter
        if (trim($body) && strpos($body, 'value')) {
            // json response
            $body = json_decode(rawurldecode($this->body));
            $this->counter = new Counter($body->value, $this->headers);
        }
    }

    /**
     * Retrieves the Location value from the response headers
     *
     * @return string
     * @throws \Widda\Riak\Command\Exception
     */
    public function getLocation()
    {
        return $this->getHeader('Location');
    }

    /**
     * @return Counter|null
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Retrieves the date of the counter's retrieval
     *
     * @return string
     * @throws \Widda\Riak\Command\Exception
     */
    public function getDate()
    {
        return $this->getHeader('Date');
    }
}