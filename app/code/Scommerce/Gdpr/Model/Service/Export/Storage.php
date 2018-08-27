<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Service\Export;

/**
 * In memory storage data for export
 *
 * Class Storage
 * @package Scommerce\Gdpr\Model\Service\Export
 */
class Storage
{
    /** @var string "Tab separator" */
    private $tab = ',';

    /** @var string "Line separator" */
    private $cr = PHP_EOL;

    /** @var Data\Record[] Remembered records */
    private $records = [];

    /**
     * @param string $tab
     * @param string $cr
     */
    public function __construct(
        $tab = ',',
        $cr = PHP_EOL
    ) {
        $this->tab = $tab;
        $this->cr = $cr;
    }

    /**
     * Add new record
     * Usage:
     * 1. $storage->addRecord('TitleString', HeaderArray, BodyArray)
     * 2. $storage->addRecord([
     *      'TitleString',
     *      HeaderArray,
     *      BodyArray
     * ])
     *
     * @param string|array $title
     * @param array $header
     * @param array $body
     * @return $this
     */
    public function addRecord($title, $header = [], $body = [])
    {
        if (is_array($title)) {
            if (empty($title)) {
                return $this;
            }
            $input = $title;
            $title = $input[0];
            $header = $input[1];
            $body = $input[2];
        }
        $arrayBody = [];
        foreach ((array) $body as $b) {
            $arrayBody[] = $b;
        }
        $this->records[] = new Data\Record((string) $title, (array) $header, $arrayBody);
        return $this;
    }

    /**
     * @return Data\Record[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Render data as string
     *
     * @return string
     */
    public function render()
    {
        $data = [];
        foreach($this->records as $record) {
            $data[] = $record->getTitle();
            $data[] = $this->renderArray($record->getHeader());
            foreach ($record->getBody() as $body) {
                $data[] = $this->renderArray($body);
            }
        }
        return implode($this->cr, $data);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Helper method for rendering array item of record
     * Each element of the array wrapped by quotation mark ""
     *
     * @param array $input
     * @return string
     */
    private function renderArray($input)
    {
        $data = [];
        foreach ($input as $element) {
            $data[] = is_array($element) ? $this->renderArray($element) : sprintf('"%s"', $element);
        }
        return implode($this->tab, $data);
    }
}
