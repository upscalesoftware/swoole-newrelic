<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic\Browser;

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
     * Inject dependencies
     * 
     * @param callable $subject
     * @param TransactionFactory $browserFactory
     */
    public function __construct(
        callable $subject,
        TransactionFactory $browserFactory
    ) {
        $this->subject = $subject;
        $this->transactionFactory = $browserFactory;
    }

    /**
     * Invoke the underlying middleware surrounding it with the transaction monitoring  
     * 
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function __invoke(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        if (!$this->isAjax($request)) {
            $transaction = $this->transactionFactory->create($response);
            $response = new Response\Observable($response);
            $response->onBodyAppend([$transaction, 'track']);
        }
        $middleware = $this->subject;
        $middleware($request, $response);
    }

    /**
     * Whether a given request is made via AJAX
     *
     * @param \Swoole\Http\Request $request
     * @return bool
     */
    protected function isAjax(\Swoole\Http\Request $request)
    {
        if (isset($request->header['x-requested-with'])) {
            return (strtolower($request->header['x-requested-with']) == 'xmlhttprequest');
        }
        return false;
    }
}