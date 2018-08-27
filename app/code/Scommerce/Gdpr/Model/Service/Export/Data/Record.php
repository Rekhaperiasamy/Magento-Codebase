<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service\Export\Data;

/**
 * One record of export data
 *
 * Class Record
 * @package Scommerce\Gdpr\Model\Service\Export\Data
 */
class Record
{
    /** @var string */
    private $title;

    /** @var array */
    private $header;

    /** @var array */
    private $body;

    /**
     * @param string $title
     * @param array $header
     * @param array $body
     */
    public function __construct(
        $title = '',
        $header = [],
        $body = []
    ) {
        $this->title = $title;
        $this->header = $header;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }
}
