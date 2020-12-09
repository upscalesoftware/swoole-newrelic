<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\Swoole\Newrelic\Apm;

use Upscale\Swoole\Reflection\Http\Response;

class TransactionDecorator
{
    /**
     * @var callable
     */
    protected $subject;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var int
     */
    protected $transactionLevel = 0;

    /**
     * @var Transaction|null
     */
    protected $transaction;

    /**
     * Inject dependencies
     * 
     * @param callable $subject
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(callable $subject, TransactionFactory $transactionFactory)
    {
        $this->subject = $subject;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * Invoke the underlying middleware surrounding it with the transaction monitoring  
     * 
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function __invoke(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        // Start transaction upon the first request
        if (!$this->transactionLevel) {
            $this->transaction = $this->transactionFactory->create($request);
            $this->transaction->start();
            $response = new Response\Observable($response);
            $response->onHeadersSentBefore([$this->transaction, 'stop']);
        }
        $this->transactionLevel++;
        $middleware = $this->subject;
        try {
            $middleware($request, $response);
        } finally {
            // Finish transaction upon completion of all coroutine requests
            $this->transactionLevel--;
            if (!$this->transactionLevel) {
                $this->transaction->finish();
                $this->transaction = null;
            }
        }
    }
}