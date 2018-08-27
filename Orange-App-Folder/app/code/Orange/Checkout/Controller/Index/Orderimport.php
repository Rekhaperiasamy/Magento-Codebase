<?php
namespace Orange\Checkout\Controller\Index;
use Orange\Selligent\Helper\Selligent;
use Magento\Framework\App\ObjectManager;
class Orderimport extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $checkoutSession;
	protected $coreRegistry;
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\App\ResourceConnection $resourceConnection,
	   \Magento\Checkout\Model\Session $checkoutSession,
	   \Magento\Framework\Registry $coreRegistry,	   
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	   \Magento\Framework\Module\Dir\Reader $moduleReader,
	\Magento\Framework\File\Csv $fileCsv
    ) {
        parent::__construct($context);
		$this->_checkoutSession = $checkoutSession;
		$this->_moduleReader = $moduleReader;
    $this->_fileCsv = $fileCsv;
		$this->_coreRegistry = $coreRegistry;
		$this->resourceConnection = $resourceConnection;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Flush cache storage
     *
     */
   public function execute()
    {
        $directory = $this->_moduleReader->getModuleDir('etc', 'Orange_Checkout'); 
		$file = $directory . '/mobistar_report_today.csv';
		
	   $connection = $this->resourceConnection->getConnection();
		$tableName = $this->resourceConnection->getTableName('importorder');
       if (file_exists($file)) {
       $data = $this->_fileCsv->getData($file);
	   
        for($i=1; $i<count($data); $i++) {
		echo "<pre>";
		
		$orderid = $data[$i][2];
		$email = $data[$i][6];
		$fname = $data[$i][7];
		$lname = $data[$i][8];
		$dob = $data[$i][38];
		if($dob)
		{
 		$newDate = str_replace('/', '-', $dob);
		$newDate = date("Y-m-d", strtotime($newDate));
		}
 		else {
			$newDate='0000-00-00 00:00:00';
		}
		//echo $newDate;
		$result = $connection->fetchAll('SELECT orderid FROM '.$tableName.' WHERE orderid='.$orderid);
		if(count($result)==0){
		$sql  = "INSERT INTO " . $tableName . " (`entity_id`, `orderid`, `firstname`, `lastname`,`email`,`dateofbirth`) VALUES (NULL, '$orderid', '$fname', '$lname','$email','$newDate')"; 
		$connection->query($sql);
		} 
        }
      }
	}
}