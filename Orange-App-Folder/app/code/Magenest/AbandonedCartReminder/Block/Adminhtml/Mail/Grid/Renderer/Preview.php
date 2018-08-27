<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 19/01/2016
 * Time: 16:01
 */
namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Mail\Grid\Renderer;

class Preview extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{


    /**
     * Renderer for "Action" column in Mail Log templates grid
     *
     * @param  \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $actions[] = [
                      'url'     => $this->getUrl('*/*/preview', ['id' => $row->getId()]),
                      'popup'   => true,
                      'caption' => __('Preview'),
                     ];

        $this->getColumn()->setActions($actions);

        return parent::render($row);

    }//end render()
}//end class
