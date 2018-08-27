<?php
namespace Orange\Crossellorange\Controller\Scoring;
use Magento\Framework\App\ObjectManager;
class Scoring extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;
    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;
    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;    
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context,               
    \Magento\Framework\App\ResourceConnection $resourceConnection,
    \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;      
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceConnection = $resourceConnection;
    }
    /**
     * Scoring Webservice
     *
     */
    public function execute() {   
        $objectManager = ObjectManager::getInstance();                
        $scoringSession = $objectManager->get('Magento\Checkout\Model\Session')->getNewcheckout();              
			//paramters for customer check
			$scoringType = filter_input(INPUT_POST, 'scoring_type');
			$HSCount = filter_input(INPUT_POST, 'hs_count');
			$ModemCount = '0';
			$Msisdn = filter_input(INPUT_POST, 'msisdn');
			$IdType = filter_input(INPUT_POST, 'id_type');
			$idCardNumber = filter_input(INPUT_POST, 'id_card_numer');
			$nationalRegisterIDNumber = filter_input(INPUT_POST, 'national_id');
			$checkDocValidation = filter_input(INPUT_POST, 'checkDocValidation');
			$fName = filter_input(INPUT_POST, 'fname');
			$lName = filter_input(INPUT_POST, 'lname');
			$dob = filter_input(INPUT_POST, 'dob');
			$Email = filter_input(INPUT_POST, 'email');
			$StreetName = filter_input(INPUT_POST, 'street_name');
			$StreetNumber = filter_input(INPUT_POST, 'street_number');
			$ZIP = filter_input(INPUT_POST, 'zip');  
			$City = filter_input(INPUT_POST, 'city');
			$Country = 'Belgium';
			$PaymentNumber = '0';
			$NbrSimCards = filter_input(INPUT_POST, 'nbr_sim_cards');     
			$requestType = 'Check';                
			$Requestor = 'Eshop';
            $vatNumber = filter_input(INPUT_POST, 'vatnumber');
			
			$idCardNumber = str_replace('-','',$idCardNumber);
			$idCardNumber = str_replace(' ','',$idCardNumber);
			$nationalRegisterIDNumber = str_replace(' ','',$nationalRegisterIDNumber);
			//get base64 encrypt value
			$nationalRegisterIDNumber = $objectManager->get('Orange\Crossellorange\Helper\Scoring')->getBase64EncryptVal($nationalRegisterIDNumber);
			$dob = str_replace('/', '-', $dob);
		    $dob = date("Y-m-d", strtotime($dob));
			
			/*echo $scoringType.$HSCount.$ModemCount.$Msisdn.$IdType.$idCardNumber.$fName.$lName.$dob.$Email.$StreetName.$StreetNumber.$ZIP.$City.$Country.$PaymentNumber.$NbrSimCards.$requestType.$Requestor; */
        if (isset($idCardNumber) && isset($fName) && isset($lName) && isset($dob) && isset($requestType) && isset($scoringType)) { 			
            $customerCheckResponse = $objectManager->get('Orange\Crossellorange\Helper\Scoring')->getCustomerCheck($idCardNumber,$fName,$lName,$dob,$requestType,$scoringType,$vatNumber);			
            if(isset($customerCheckResponse) && $customerCheckResponse == "FALSE"){
                echo "FALSE" ;
				$this->deleteCartItem();
				die;
            }
            else {
	           $customerStatus = $customerCheckResponse['customerstatus'];
			   $ScoringWebserviceResponse = $objectManager->get('Orange\Crossellorange\Helper\Scoring')->getScoringCheck($Email,$idCardNumber,$fName,$lName,$dob,$HSCount,$scoringType,$ModemCount,$Msisdn,$IdType,$StreetName,$StreetNumber,$ZIP,$City,$Country,$PaymentNumber,$NbrSimCards,$Requestor,$vatNumber,$customerStatus, $nationalRegisterIDNumber, $checkDocValidation);
                if($ScoringWebserviceResponse == 'DEC'){
                    echo "FALSE";
					$this->deleteCartItem();
					die;
                } else {
				    if ($ScoringWebserviceResponse) {
						echo $scoringSession['data'] = $ScoringWebserviceResponse;
						die;
					} else {
						$this->deleteCartItem();
						die;
					}
                }
            }
		}
    }
    public function deleteCartItem() {
	   
		$objectManager = ObjectManager::getInstance(); 
		$quote = $objectManager->create('Magento\Checkout\Model\Cart')->getQuote();
		$model = $this->_objectManager->create('Orange\Abandonexport\Model\Items');
		$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$quote->getId());
		if ($abandonexport->count() > 0 ) {
			$id = $abandonexport->getFirstItem()->getId();
			$abandonexport = $this->_objectManager->create('Orange\Abandonexport\Model\Items')->load($id);
				
			$abandonexport->setOrderStatus('orderRefusal');
			$abandonexport->save();
		}
		$quote->setIsActive(false);
		$quote->save();
		$objectManager->get('Magento\Checkout\Model\Session')->unsNewcheckout();
		$objectManager->get('Magento\Checkout\Model\Session')->clearQuote();
	    //$quote->delete(); // delete the quote item
    }
	public function getItemModel(){
		$objectManager = ObjectManager::getInstance(); 
		$itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
		return $itemModel;
	}
}

