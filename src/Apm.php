<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic;

/**
 * New Relic APM (Application Performance Monitoring) instrumentation
 */
class Apm
{
    /**
     * @var Apm\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * Inject dependencies
     * 
     * @param Apm\TransactionFactory $transactionFactory
     */
    public function __construct(Apm\TransactionFactory $transactionFactory)
    {
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * Install monitoring instrumentation
     *
     * @param \Swoole\Http\Server $server
     * @throws \UnexpectedValueException
     */
    public function instrument(\Swoole\Http\Server $server)
    {
        // Dismiss monitoring unaware of transaction boundaries of the event loop execution model 
        newrelic_end_transaction(true);
        
        $server = new \Upscale\Swoole\Reflection\Http\Server($server);
        $server->setMiddleware($this->monitor($server->getMiddleware()));
    }

    /**
     * Decorate a given middleware with monitoring instrumentation
     * 
     * @param callable $middleware
     * @return callable
     */
    public function monitor(callable $middleware)
    {
        return new Apm\TransactionDecorator($middleware, $this->transactionFactory);
    }
}