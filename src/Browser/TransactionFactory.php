<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic\Browser;

class TransactionFactory
{
    /**
     * @var string
     */
    protected $defaultMimeType;

    /**
     * Inject dependencies
     *
     * @param string $defaultMimeType Default Content-Type response header 
     * @link https://github.com/swoole/swoole-src/blob/master/swoole_http2_server.cc
     */
    public function __construct($defaultMimeType = 'text/html')
    {
        $this->defaultMimeType = $defaultMimeType;
    }
    
    /**
     * Create a new transaction monitoring a given response
     *
     * @param \Swoole\Http\Response $response 
     * @return Transaction 
     */
    public function create(\Swoole\Http\Response $response)
    {
        return new Transaction($response, new Beacon(), $this->defaultMimeType);
    }
}