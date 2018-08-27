<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model\Config\Source;

class PostDataKey implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    protected $array = [];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (empty($array)) {
            $array = [];

            foreach ($this->getOptions() as $option) {
                $array[] = [
                    'label' => $option,
                    'value' => $option,
                ];
            }

            $this->array = $array;
        }

        return $this->array;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'MAC',
            'date',
            'TPE',
            'montant',
            'reference',
            'texte-libre',
            'code-retour',
            'cvx',
            'vld',
            'brand',
            'status3ds',
            'numauto',
            'motifrefus',
            'originecb',
            'bincb',
            'hpancb',
            'ipclient',
            'originetr',
            'veres',
            'pares',
            'montantech',
            'filtragecause',
            'filtragevaleur',
            'cbenregistree',
            'cbmasquee',
            'modepaiement',
        ];
    }
}
