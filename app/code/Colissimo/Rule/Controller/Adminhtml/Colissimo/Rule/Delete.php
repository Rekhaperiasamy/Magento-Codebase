<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule;

use Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule as AbstractRule;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 */
class Delete extends AbstractRule
{
    /**
     * Delete Colissimo rule action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Colissimo\Rule\Model\Rule');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the rule.'));
                $this->_redirect('colissimo_rule/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('colissimo_rule/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));
        $this->_redirect('colissimo_rule/*/');
    }
}
