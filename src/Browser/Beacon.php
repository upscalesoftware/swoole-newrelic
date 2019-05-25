<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See COPYRIGHT.txt for license details.
 */
namespace Upscale\Swoole\Newrelic\Browser;

/**
 * New Relic Browser tracking pixel
 * 
 * @link https://docs.newrelic.com/docs/browser/new-relic-browser/page-load-timing-resources/instrumentation-browser-monitoring
 */
class Beacon
{
    /**
     * Insert browser instrumentation into a given HTML content
     * 
     * @param string $html
     * @return string
     */
    public function insert($html)
    {
        // Dynamically render transaction-specific scripts
        $header = newrelic_get_browser_timing_header();
        if ($header) {
            $html = $this->insertHeader($html, $header);
        }
        $footer = newrelic_get_browser_timing_footer();
        if ($footer) {
            $html = $this->insertFooter($html, $footer);
        }
        return $html;
    }

    /**
     * Insert a given code snippet into HTML head
     * 
     * @param string $html
     * @param string $snippet
     * @return string
     * 
     * @link https://docs.newrelic.com/docs/agents/php-agent/php-agent-api/newrelic_get_browser_timing_header
     * @link https://docs.newrelic.com/docs/browser/new-relic-browser/page-load-timing-resources/instrumentation-browser-monitoring#javascript-placement
     * @link https://docs.newrelic.com/docs/browser/new-relic-browser/installation/install-new-relic-browser-agent#copy-paste-app
     */
    protected function insertHeader($html, $snippet)
    {
        $headClosePos = stripos($html, '</head>');
        if ($headClosePos) {
            $firstScriptPos = stripos($html, '<script');
            $snippetPos = $firstScriptPos ? min($firstScriptPos, $headClosePos) : $headClosePos;
            $html = $this->insertSnippet($html, $snippet, $snippetPos);
        }
        return $html;
    }

    /**
     * Insert a given code snippet into HTML body
     * 
     * @param string $html
     * @param string $snippet
     * @return string
     * @link https://docs.newrelic.com/docs/agents/php-agent/php-agent-api/newrelic_get_browser_timing_footer
     */
    protected function insertFooter($html, $snippet)
    {
        $bodyClosePos = strripos($html, '</body>');
        if ($bodyClosePos) {
            $html = $this->insertSnippet($html, $snippet, $bodyClosePos);
        }
        return $html;
    }

    /**
     * Insert a snippet into a string at a given position 
     * 
     * @param string $string
     * @param string $snippet
     * @param int $position
     * @return string
     */
    protected function insertSnippet($string, $snippet, $position)
    {
        return substr_replace($string, $snippet, $position, 0);
    }
}