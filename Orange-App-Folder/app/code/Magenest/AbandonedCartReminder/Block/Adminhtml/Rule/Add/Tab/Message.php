<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/06/2016
 * Time: 13:49
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add\Tab;

class Message extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_template = 'rule/add/tab/message.phtml';

    /**
     * @var \Magenest\AbandonedCartReminder\Model\Resource\Message\CollectionFactory
     */
    protected $messageCollectionFactory;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magenest\AbandonedCartReminder\Model\Resource\Message\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory);

        $this->messageCollectionFactory = $collectionFactory;

    }//end __construct()


    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('SMS');

    }//end getTabLabel()


    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('SMS');

    }//end getTabTitle()


    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;

    }//end canShowTab()


    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;

    }//end isHidden()


    /**
     * @param $rule \Magenest\AbandonedCartReminder\Model\Rule
     * @return mixed
     */
    public function getSmsCollection()
    {
        /** @var  $rule \Magenest\AbandonedCartReminder\Model\Rule */
        $rule   = $this->_coreRegistry->registry('current_fue_rule');
        $smsIds = $rule->getSMSData();
        return $this->messageCollectionFactory->create()->getSMSCollectionByIds($smsIds);

    }//end getSmsCollection()
}//end class
