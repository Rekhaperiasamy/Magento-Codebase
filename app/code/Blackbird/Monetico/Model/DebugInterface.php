<?php

namespace Blackbird\Monetico\Model;

interface DebugInterface
{
    /**
     * Add a record in the monetico logs if the debug mode is enabled
     *
     * @param string $message
     * @param array $context
     * @return $this
     */
    public function addDebugMessage($message, $context = []);
}
