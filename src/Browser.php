<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic;

/**
 * New Relic Browser aka Real User Monitoring (RUM) instrumentation
 */
class Browser
{
    /**
     * @var Browser\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * Inject dependencies
     * 
     * @param Browser\TransactionFactory $transactionFactory
     */
    public function __construct(Browser\TransactionFactory $transactionFactory)
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
        newrelic_disable_autorum();
        
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
        return new Browser\TransactionDecorator($middleware, $this->transactionFactory);
    }
}