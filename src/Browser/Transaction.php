<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic\Browser;

/**
 * New Relic Browser transaction
 */
class Transaction
{
    /**
     * @var \Swoole\Http\Response
     */
    protected $response;
    
    /**
     * @var Beacon
     */
    protected $beacon;
    
    /**
     * Inject dependencies
     * 
     * @param \Swoole\Http\Response $response
     * @param Beacon $beacon
     */
    public function __construct(\Swoole\Http\Response $response, Beacon $beacon)
    {
        $this->response = $response;
        $this->beacon = $beacon;
    }
    
    /**
     * Instrument response body for browser monitoring  
     * 
     * @param string $content
     */
    public function track(&$content)
    {
        if ($this->isHtml($this->response)) {
            $content = $this->beacon->insert($content);
        }
    }

    /**
     * Whether a given response body has an HTML format
     * 
     * @param \Swoole\Http\Response $response
     * @return bool
     */
    protected function isHtml(\Swoole\Http\Response $response)
    {
        $mimeType = $this->getMimeType((array)$response->header);
        return (stripos($mimeType, 'text/html') === 0);
    }

    /**
     * Detect MIME type from given headers
     * 
     * @param array $headers
     * @return string|null
     */
    protected function getMimeType(array $headers)
    {
        foreach ($headers as $name => $value) {
            if (strtolower($name) == 'content-type') {
                return $value;
            }
        }
        return null;
    }
}