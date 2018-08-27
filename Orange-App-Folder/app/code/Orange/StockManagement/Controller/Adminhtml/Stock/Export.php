<?php
namespace Orange\StockManagement\Controller\Adminhtml\Stock;
class Export extends \Magento\Backend\App\Action {

    protected $_fileFactory;
    protected $_response;
    protected $_view;
    protected $directory;
    protected $converter;
    protected $resultPageFactory ;
    protected $directory_list;
    //protected $_objectManager;


    public function __construct(\Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory  = $resultPageFactory;
        //$this->_objectManager  = $objectManager;
    }
    
    public function execute() {
        
        $fileName = "Product-Stock-Summary-".date('YmdHis').".csv";
        $csvHeaders = 'SKU, NAME, QTY,STATUS';
        
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = fopen( 'php://output', 'w' );
        $productcontents = $this->productInfo();
        $csvContent = $this->csvData($productcontents);
        
        fputcsv($fh,explode(',',$csvHeaders));
        foreach($csvContent as $arr) {
            fputcsv($fh,explode(',',$arr));
        }
        fclose($fh);
        die;
    }
    
    public function productInfo()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $blockInstance = $objectManager->get('Orange\StockManagement\Block\Adminhtml\Stockexport');
        $content = $blockInstance->getProductData();
        return $content;
    }
    
    public function csvData($rowContents){
        $csvRow = array();
        foreach ($rowContents as $data)   {
//            if($data->getIsInStock()==0){
//                $stock ='Out of stock';
//            }else{
//                $stock = 'In stock';
//            }
            /* product status */
            if($data->getStatus()==1){
                $prod_stat ='Enabled';
            }else{
                $prod_stat = 'Disabled';
            }
            /* end */
            
            
            $csvRow[]= $data->getSku().','.$data->getName().','.$data->getQty().','.$prod_stat;
        }
        return $csvRow;
    }

   
}
