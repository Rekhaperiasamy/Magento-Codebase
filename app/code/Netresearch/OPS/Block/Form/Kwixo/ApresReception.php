<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Form\Kwixo;

class ApresReception extends \Netresearch\OPS\Block\Form
{
    const FRONTEND_TEMPLATE = 'Netresearch_OPS::ops/form/kwixo/apres_reception.phtml';

    protected $pmLogo = 'Netresearch_OPS::images/kwixo/apres_reception.jpg';

    /**
     * Init OPS payment form
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::FRONTEND_TEMPLATE);
    }
}
