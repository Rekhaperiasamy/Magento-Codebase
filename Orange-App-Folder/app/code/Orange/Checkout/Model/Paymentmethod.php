<?php
 
namespace Orange\Checkout\Model;
 
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;
 
class Paymentmethod extends \Magento\Framework\DataObject 
    implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;
    /**
     * @var Config
     */
    protected $_paymentModelConfig;
     
    /**
     * @param ScopeConfigInterface $appConfigScopeConfigInterface
     * @param Config               $paymentModelConfig
     */
    public function __construct(
        ScopeConfigInterface $appConfigScopeConfigInterface,
        Config $paymentModelConfig
    ) {
 
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
    }
  
    public function toOptionArray()
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = array();				
        foreach ($payments as $paymentCode => $paymentModel) {
			if($paymentCode!='free'):
				$paymentTitle = $this->_appConfigScopeConfigInterface
					->getValue('payment/'.$paymentCode.'/title');
				$paymentDescription = $this->_appConfigScopeConfigInterface
					->getValue('payment/'.$paymentCode.'/title');
				$paymentType = $this->_appConfigScopeConfigInterface
					->getValue('payment/'.$paymentCode.'/payment_type');
				$paymentImage = $this->_appConfigScopeConfigInterface
					->getValue('payment/'.$paymentCode.'/show_image');
				$methods[$paymentCode] = array(
					'label' => $paymentTitle,
					'value' => $paymentCode,
					'description' => $paymentDescription,
					'payment_type' => $paymentType,
					'show_image' => $paymentImage
				);
			endif;
        }		
        return $methods;
    }	
}