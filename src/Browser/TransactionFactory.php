<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic\Browser;

class TransactionFactory
{
    /**
     * Create a new transaction monitoring a given response
     *
     * @param \Swoole\Http\Response $response 
     * @return Transaction 
     */
    public function create(\Swoole\Http\Response $response)
    {
        return new Transaction($response, new Beacon());
    }
}