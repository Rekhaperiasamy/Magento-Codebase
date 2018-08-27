<?php
namespace Tealium\Tags\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
 
class NewUdo extends Udo   
{
    public function __construct(
        Context $context,
        array $data = []
            
            //,
        //ExistingUdo $existingUdo
   
        /*
         * Any other needed dependencies to inject go here
         */
    ) {
        //$this->merge($existingUdo->getUdoData());
        
        $this->merge([
           /*
            * Fill in data here using injected system objects, e.g:
            * 
            * "page_type" => "home",
            * "page_name" => "Home",
            * "site_currency" => "USD",
            * "site_region" => "us",
            *  
            *     . . .
            */ 
        ]);
        
        parent::__construct($context, $data);
    }
}