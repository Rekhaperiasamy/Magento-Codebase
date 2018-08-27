<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab;

/**
 * Class Schedule
 * @package Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab
 */
class Schedule extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Aheadworks\Csblock\Model\Csblock */
        $model = $this->_coreRegistry->registry('aw_csblock_model');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('csblock_');

        $fieldset = $form->addFieldset(
            'display_fieldset',
            [
                'legend' => __('Display Block'),
            ]
        );

        $dateFormatIso = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $fieldset->addField(
            'date_from',
            'date',
            [
                'name' => 'date_from',
                'label' => __('From Date'),
                'title' => __('From Date'),
                'date_format' => $dateFormatIso,
            ]
        );

        $fieldset->addField(
            'date_to',
            'date',
            [
                'name' => 'date_to',
                'label' => __('To Date'),
                'title' => __('To Date'),
                'date_format' => $dateFormatIso,
            ]
        );

        $fieldset = $form->addFieldset(
            'pattern_fieldset',
            [
                'legend' => __('Schedule Pattern'),
            ]
        );

        $patternSource = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Aheadworks\Csblock\Model\Source\Pattern');
        $fieldset->addField(
            'pattern',
            'select',
            [
                'name' => 'pattern',
                'label' => __('Show'),
                'title' => __('Show'),
                'options' => $patternSource->getOptionArray()
            ]
        );

        $timeFormatIso = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );
        $fieldset->addField(
            'time_from',
            'time',
            [
                'name' => 'time_from',
                'label' => __('From Time'),
                'title' => __('From Time'),
                'time_format' => $timeFormatIso,
            ]
        );

        $fieldset->addField(
            'time_to',
            'time',
            [
                'name' => 'time_to',
                'label' => __('To Time'),
                'title' => __('To Time'),
                'time_format' => $timeFormatIso,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function initForm()
    {
        $this->_prepareForm();
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Schedule');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Schedule');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
