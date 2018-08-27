<?php
namespace Orange\Upload\Controller\Adminhtml\Header;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Symfony\Component\Console\Command\Command;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\Import\Source\CsvFactory;
use Magento\Framework\Filesystem\Directory\ReadFactory;

class Csvupload extends Action {

    private $state;
 
    /**
     * @var Import $importFactory
     */
    protected $importFactory;
 
    /**
     * @var CsvFactory
     */
    private $csvSourceFactory;
 
    /**
     * @var ReadFactory
     */
    private $readFactory;
 
    /**
     * Constructor
     *
     * @param State $state  A Magento app State instance
     * @param ImportFactory $importFactory Factory to create entiry importer
     * @param CsvFactory $csvSourceFactory Factory to read CSV files
     * @param ReadFactory $readFactory Factory to read files from filesystem     *
     * @return void
     */
    public $context;
    
    protected $_resourceConnection;
    public $urlpath;
    public $imgPath;

    public function __construct(\Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\App\ResourceConnection $resourceConnection,
            \Magento\Catalog\Model\ProductRepository $productRepository, 
            State $state, ImportFactory $importFactory, CsvFactory $csvSourceFactory, ReadFactory $readFactory
    ) {
        $this->_resourceConnection = $resourceConnection->getConnection();
        $this->state = $state;
        $this->importFactory = $importFactory;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->readFactory = $readFactory;
        $this->productRepository = $productRepository;      
        $this->imgPath = array();
		$this->urlpath = array();
        return parent::__construct($context);
    }
    
    /*
     * Function used for reading Input CSV file
     */

    public function execute() {

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('upload/header/subsidyprice');

        if ($this->getRequest()->getParams('key')) {

            if (isset($_FILES["csv_file"]["name"])) {
                $filename = $_FILES["csv_file"]["name"];
                $source = $_FILES["csv_file"]["tmp_name"];
                $type = $_FILES["csv_file"]["type"];
                $name = explode(".", $filename);
                $mimes = array('application/vnd.ms-excel', 'text/csv');

                if (in_array($type, $mimes)) {

                    try {
                        $fh = fopen($_FILES['csv_file']['tmp_name'], 'r+');
                        if (!$fh) {
                            throw new Exception('File open failed. Please check the file permission');
                        }

                        $all_rows = array();
                        $header = null;
                        $mainIds = array();
                        $infos = array();
                        $i = 0;

                        while ($row = fgetcsv($fh, 8192)) {
                            
                            if ($header === null) {
                                $header = $row;
                                continue;
                            }

                            if (isset($header[0]) && $header[0] == 'device_sku' && isset($header[1]) && $header[1] == 'plan_sku' && isset($header[2]) && $header[2] == 'price' && isset($header[3]) && $header[3] == 'soho_price' && isset($header[4]) && $header[4] == 'context') {
                                $all_rows[] = array_combine($header, $row);
                                $infos[] = $row[0] . ',' . $row[1] . ',' . $row[2]. ',' . $row[3] . ',' . $row[4];
                            }
                        }
                        fclose($fh);

                        $prefix = strtotime("now");
                        if(isset($infos)){
                            $fp = fopen(BP . '/pub/media/' . $prefix . '_uploaded.csv', 'w');
                            foreach ($infos as $info) {
                                fputcsv($fp, array($info), ',', ' ');
                            }
                            fclose($fp);
                        }

                        $result = array();
                        $upsell = array();

                        if (isset($all_rows) && count($all_rows)) {
                            foreach ($all_rows as $rowval) {
                                $headerRow['0'] = $this->headerRow();
                                $result[] = $this->createBundleProduct($rowval, $i);
                                $i++;
                            }

                            if (count($result)) {
                                $flagChecking = $this->bundledProductsDeleteFlagSetting();
                                if ($flagChecking) {
                                    $result = array_filter($result);

                                    //below function is used to delete URL's in url_rewrite table
                                    $this->unwantedURLDeletion();
                                    //Create Bundle products ss
                                    $this->csvCreation($result, $prefix . '_bundle_catalog.csv', 'catalog', $headerRow);
                                    $this->importCatalog('media/' . $prefix . '_bundle_catalog.csv', 'create');
                                    $this->imageadding();
                                    $this->bundledProductsCheck();
                                } else {
                                    $this->messageManager->addError('Error in Flag Setting');
                                    return $resultRedirect;
                                }
                            } else {
                                $this->messageManager->addError('CSV value count is null');
                                return $resultRedirect;
                            }

                            foreach ($all_rows as $rowData) {
                                $upsell[] = $this->createUpsellProduct($rowData);
                            }
                            if (count($upsell)) {
                                $upsell = array_filter($upsell);

                                if (count($upsell)) {
                                    $this->csvCreation($upsell, $prefix . '_upsell_bundle_catalog.csv', 'upsell', $this->headerRowupsell());
                                    $this->importCatalog('media/' . $prefix . '_upsell_bundle_catalog.csv', 'create');
                                }
                                $this->removeBundledProducts();
                            } else {
                                $this->messageManager->addError('UpSell Creation is not done.');
                            }
                        } else {
                            $this->messageManager->addError('Error in CSV File');
                            return $resultRedirect;
                        }
                    } catch (Exception $ex) {
                        $this->messageManager->addError('Some thing went wrong.' . $ex->getMessage());
                        return $resultRedirect;
                    }
                } else {
                    $this->messageManager->addError('Invalid file has been uploaded. Please upload CSV with proper data');
                    return $resultRedirect;
                }
            } else {
                $this->messageManager->addError('CSV upload Failed. Please try again.');
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addError('There was a problem with the upload. Please try again.');
            return $resultRedirect;
        }

        return $resultRedirect;
    }

    public function unwantedURLDeletion() {
        if (count($this->urlpath)) {
            $path = array();
            foreach ($this->urlpath as $pathval) {
                foreach ($pathval as $val) {
                    if ($val) {
                        $path[] = $val;
                    }
                }
            }
            $pathv = implode('","', $path);
            $fSQL = 'select url_rewrite_id from url_rewrite WHERE request_path IN ("' . $pathv . '")';
            $sQLData = $this->_resourceConnection->fetchAll($fSQL);

            if (count($sQLData)) {
                $deleteId = array();
                foreach ($sQLData as $data) {
                    $deleteId[] = $data['url_rewrite_id'];
                }
                $dID = implode('","', $deleteId);
                $deleteTable = 'delete from url_rewrite where url_rewrite_id IN("' . $dID . '");';
                $this->_resourceConnection->query($deleteTable);
            }
        }

        return true;
    }

    public function bundledProductsDeleteFlagSetting() {
        $flagSQL = 'select row_id from catalog_product_entity WHERE row_id IN ( SELECT ei.row_id FROM catalog_product_entity_int ei, eav_attribute ea WHERE ea.attribute_code="bundle_delete" AND ei.attribute_id = ea.attribute_id AND value = 0 )';
        $flagSQLData = $this->_resourceConnection->fetchAll($flagSQL);

        if (count($flagSQLData)) {
            $attrIDSql = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'bundle_delete'";
            $attribute_id = $this->_resourceConnection->fetchRow($attrIDSql);
            $attrId = $attribute_id['attribute_id'];
            $rowID = array();
            if ($attrId != '') {
                foreach ($flagSQLData as $flag) {
                    $updateTabSql = 'UPDATE catalog_product_entity_int SET value = 1 WHERE attribute_id = "' . $attrId . '" AND row_id  = "' . $flag['row_id'] . '"';
                    $this->_resourceConnection->query($updateTabSql);
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /*
     * fetch image from simple product
     */
    public function imageadding() {
        $imageDataval = $this->imgPath;		
		
        foreach ($imageDataval as $key => $imageData) {
            $idData = "SELECT entity_id,row_id FROM catalog_product_entity WHERE sku ='" . $key . "' LIMIT 1";
            $id = $this->_resourceConnection->fetchRow($idData);

            if (isset($id['row_id'])) {
                $childvarQry = 'INSERT IGNORE INTO catalog_product_entity_varchar (value_id, attribute_id, store_id, row_id, value)VALUES'
                        . '("",87,0,"' . $id['row_id'] . '","' . $imageData['base'] . '"),'
                        . '("",88,0,"' . $id['row_id'] . '","' . $imageData['base'] . '"),'
                        . '("",89,0,"' . $id['row_id'] . '","' . $imageData['base'] . '")';
                $this->_resourceConnection->query($childvarQry);
                //$imagefetch = array_push($imageData['additional_images'], $imageData['base']);
	
                foreach ($imageData['additional_images'] as $key1 => $img) {
                    $galleryQ = 'INSERT IGNORE INTO catalog_product_entity_media_gallery(value_id,attribute_id,value,media_type,disabled)VALUES("",90,"' . $img . '","image",0)';
                    $this->_resourceConnection->query($galleryQ);
                    $galleryQSel = "SELECT value_id FROM catalog_product_entity_media_gallery ORDER BY value_id DESC LIMIT 1";
                    $galleryQSelValueId = $this->_resourceConnection->fetchRow($galleryQSel);
                    $galleryValQ = 'INSERT IGNORE INTO catalog_product_entity_media_gallery_value(value_id,store_id,row_id,label,position,disabled,record_id)'
                            . 'VALUES("' . $galleryQSelValueId['value_id'] . '",0,"' . $id['row_id'] . '","' . 'test' . '","' . $key1 . '",0,"")';
                    $this->_resourceConnection->query($galleryValQ);
                    $galleryValQEntity = 'INSERT IGNORE INTO catalog_product_entity_media_gallery_value_to_entity(value_id,row_id)'
                            . 'VALUES("' . $galleryQSelValueId['value_id'] . '","' . $id['row_id'] . '")';
                    $this->_resourceConnection->query($galleryValQEntity);
                }
            }
        }
    }

    /*
     * Import product
     */
    public function importCatalog($import_path, $action) {

        $import_file = pathinfo($import_path);

        $import = $this->importFactory->create();

        $import->setData(
                array(
                    'entity' => 'catalog_product',
                    'behavior' => 'append',
                    'validation_strategy' => 'validation-stop-on-errors',
                )
        );

        $read_file = $this->readFactory->create($import_file['dirname']);
        $csvSource = $this->csvSourceFactory->create(
                array(
                    'file' => $import_file['basename'],
                    'directory' => $read_file,
                )
        );

        $validate = $import->validateSource($csvSource);
        
        if (!$validate) {
            $this->messageManager->addError('Unable to validate ' . $import_file['basename'] . ' the CSV.');
			
        } 

        $result = $import->importSource();
        if ($result) {
            $import->invalidateIndex();	   
			$this->messageManager->addSuccess($import_file['basename'].' CSV updated successfully.');			
        }		
        
		
    }

    /*
     * CSV Creation and stored in common-header
     */
    public function csvCreation($data, $file, $cat, $headerRow) {

        if ($cat == 'catalog') {
            $newfilePath1 = BP . '/pub/media/' . $file;
            $fp = fopen($newfilePath1, 'w');

            $data = array_merge($headerRow, $data);
            foreach ($data as $key1 => $res) {
                if ($key1 == 0) {
                    fputcsv($fp, $res, ',');
                    continue;
                }
                if (empty($res)) {
                    continue;
                } else {
                    foreach ($res as $skuBased) {
                        foreach ($skuBased as $line) {
                            fputcsv($fp, $line, ',');
                        }
                    }
                }
            }
            fclose($fp);
            chmod($newfilePath1, 0777);
        } else if ($cat == 'upsell') {
            $data = array_merge($headerRow, $data);
            $newfilePath = BP . '/pub/media/' . $file;
            $fp1 = fopen($newfilePath, 'w');
            foreach ($data as $key => $line) {
                if ($key == 0) {
                    fputcsv($fp1, $line, ',');
                } else {
                    if (empty($line)) {
                        continue;
                    } else {
                        foreach ($line as $val) {
                            fputcsv($fp1, $val, ',');
                        }
                    }
                }
            }
            fclose($fp1);
            chmod($newfilePath, 0777);
        } else {
            $this->messageManager->addSuccess('Something went wrong, Please check the CSV SHEET.');
        }
    }

    /*
     * Bundle Product CSV file Header part
     */
    public function headerRow() {
        return array('sku', 'store_view_code', 'attribute_set_code', 'product_type', 'categories', 'product_websites', 'name',
            'description', 'short_description', 'weight', 'product_online', 'tax_class_name', 'visibility', 'price','soho_price',
            'special_price', 'special_price_from_date', 'special_price_to_date', 'url_key', 'meta_title', 'meta_keywords',
            'meta_description', 'created_at', 'updated_at', 'new_from_date', 'new_to_date', 'display_product_options_in',
            'map_price', 'msrp_price', 'map_enabled', 'gift_message_available', 'custom_design', 'custom_design_from',
            'custom_design_to', 'custom_layout_update', 'page_layout', 'product_options_container',
            'msrp_display_actual_price_type', 'country_of_manufacture', 'additional_attributes',
            'qty', 'out_of_stock_qty', 'use_config_min_qty', 'is_qty_decimal', 'allow_backorders',
            'use_config_backorders', 'min_cart_qty', 'use_config_min_sale_qty', 'max_cart_qty',
            'use_config_max_sale_qty', 'is_in_stock', 'notify_on_stock_below', 'use_config_notify_stock_qty', 'manage_stock',
            'use_config_manage_stock', 'use_config_qty_increments', 'qty_increments', 'use_config_enable_qty_inc',
            'enable_qty_increments', 'is_decimal_divided', 'website_id', 'deferred_stock_update',
            'use_config_deferred_stock_update', 'related_skus', 'crosssell_skus', 'upsell_skus', 'hide_from_product_page',
            'custom_options', 'bundle_price_type', 'bundle_sku_type', 'bundle_price_view', 'bundle_weight_type',
            'bundle_values', 'product_icon_image', 'shipment_type','custom_bundle','handset_family','context_visibility','bundle_delete');
    }
    
    /*
     * Upsell CSV Header Part
     */

    public function headerRowupsell() {
        return array('0' => array('sku', 'upsell_skus'));
    }

    /*
     * Delete Existing Bundle products - Based on attribute value
     */
    public function removeBundledProducts() {
                
        $deleteSQL = 'select entity_id from catalog_product_entity WHERE row_id IN ( SELECT ei.row_id FROM catalog_product_entity_int ei, eav_attribute ea WHERE ea.attribute_code="custom_bundle" AND ei.attribute_id = ea.attribute_id AND value = 1 ) AND row_id IN ( SELECT ei.row_id FROM catalog_product_entity_int ei, eav_attribute ea WHERE ea.attribute_code="bundle_delete" AND ei.attribute_id = ea.attribute_id AND value = 1 )';
        $deleteSQLData = $this->_resourceConnection->fetchAll($deleteSQL);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $productCollection->addFieldToFilter('entity_id', array('in' => $deleteSQLData));
        $products = $productCollection->load();

        if (count($deleteSQLData)) {
            if (!empty($products)) {
                foreach ($products as $_product) {
                    $_product->delete();
                }
            }
        }
    }

    public function productCheckerWithOutNUll() {
        $deleteSQL = 'select entity_id,row_id from catalog_product_entity WHERE row_id IN ( SELECT ei.row_id FROM catalog_product_entity_int ei, eav_attribute ea WHERE ea.attribute_code="custom_bundle" AND ei.attribute_id = ea.attribute_id AND value = 1 )';
        $deleteSQLData = $this->_resourceConnection->fetchAll($deleteSQL);
        if (count($deleteSQLData)) {
            foreach ($deleteSQLData as $data) {
                $product = $this->productRepository->getById($data['entity_id']);
                $this->productRepository->delete($product);
            }
        }
    }

    public function bundledProductsCheck() {
        $sQL = 'SELECT S.row_id FROM catalog_product_entity S WHERE S.row_id NOT IN(SELECT row_id FROM catalog_product_entity_varchar)';
        $sQLDat = $this->_resourceConnection->fetchAll($sQL);
        if (count($sQLDat)) {
            foreach ($sQLDat as $data) {
                $deleteSQL = 'delete from catalog_product_entity where row_id = ' . $data['row_id'];
                $this->_resourceConnection->query($deleteSQL);
            }
        }
    }

    /*
     * upsell setting for virtual product and simple product
     */

    public function createUpsellProduct($data) {
        if (isset($data['device_sku']) && isset($data['plan_sku'])) {
            $dsku = $data['device_sku'];
            $vsku = $data['plan_sku'];
            $bsku = $dsku . ' + ' . $vsku;
            $check = $this->productAvailablityChecking($data['device_sku'], $data['plan_sku']);
            if ($check['device']['COUNT(*)'] == '1' && $check['virtual']['COUNT(*)'] == '1' && $check['bundle']['COUNT(*)'] == '1') {
                $row['dsku'] = array($dsku, $bsku);
                $row['vsku'] = array($vsku, $bsku);
                return $row;
            }
        }
    }

    /*
     * check product is available in catalog or not
     */
    public function productAvailablityChecking($dsku, $vsku) {
        $bsku = $dsku . ' + ' . $vsku;
        $dsql = "SELECT COUNT(*) FROM catalog_product_entity WHERE sku ='" . $dsku . "' LIMIT 1";
        $sqldata['device'] = $this->_resourceConnection->fetchRow($dsql);
        $vsql = "SELECT COUNT(*) FROM catalog_product_entity WHERE sku ='" . $vsku . "' LIMIT 1";
        $sqldata['virtual'] = $this->_resourceConnection->fetchRow($vsql);
        $bundlesql = "SELECT COUNT(*) FROM catalog_product_entity WHERE sku ='" . $bsku . "' LIMIT 1";
        $sqldata['bundle'] = $this->_resourceConnection->fetchRow($bundlesql);
        return $sqldata;
    }

    /**
     * CSV for bundle Product based on row value
     * 
     */
    public function createBundleProduct($data, $valid) {
        $storeloop = array('default', 'fr', 'nl'); 
        if(isset($data['device_sku']) && $data['plan_sku']){
        $check = $this->productAvailablityChecking($data['device_sku'],$data['plan_sku']);        
        if($check['device']['COUNT(*)'] == '1' && $check['virtual']['COUNT(*)'] == '1'){ 
		$sku = '';
                $dsku = '';
                $vsku = '';
                $name = '';
                $deviceName = '';
                $planName = '';
                $sohoPrice = '';
        if(isset($data['soho_price']) && $data['soho_price'] != ''){
            $sohoPrice = $data['soho_price'];
        }else{
            $sohoPrice = $data['price'];
        }  
        
        foreach ($storeloop as $loop) {
           if($loop == 'default'){ $code = '' ;}else { $code = $loop; }
            $dsku = $data['device_sku'];
            $vsku = $data['plan_sku'];
            $sku = $dsku . ' + ' . $vsku;
            $store_view_code = $code;
            $attribute_set_code = 'Default';
            $product_type = 'bundle';
            $categories = '';
            if($loop == 'default'){ $product_websites = 'base'; }else { $product_websites = ''; }
            $deviceName = $this->getDetaillsFromVarCharTable($data['device_sku'], $loop, '73');
            $planName = $this->getDetaillsFromVarCharTable($data['plan_sku'], $loop, '73');
            $name = $deviceName . ' + ' . $planName;
            $description = $this->getDetaillsFromTextTable($data['device_sku'], $loop, '338');
            $short_description = $this->getDetaillsFromTextTable($data['device_sku'], $loop, '85');
            if($loop == 'default'){ $weight = 1; }else {$weight= ''; }
            if($loop == 'default'){ $product_online = $this->getProductStatusInfor($data['device_sku']); }
            if($loop == 'default'){ $tax_class_name = 'Taxable Goods'; }else {$tax_class_name= ''; }
            if($loop == 'default'){ $visibility = 'Catalog, Search'; }else {$visibility= ''; }
            if($loop == 'default'){ $price = $data['price']; }else {$price= ''; }            
            if($loop == 'default'){ $soho_priceVal = $sohoPrice; }else {$soho_priceVal = ''; }
            if($loop == 'default'){ $special_price = ''; }else {$special_price= ''; }
            if($loop == 'default'){ $special_price_from_date = ''; }else {$special_price_from_date= ''; }
            if($loop == 'default'){ $special_price_to_date = ''; }else {$special_price_to_date= ''; }
            if($loop == 'default' || $loop == 'fr'){ $url_key = strtolower($this->getDetaillsFromVarCharTableURL($data['device_sku'], $data['plan_sku'], $loop, '124')); } else { $url_key = ''; }
			$this->urlpath[$sku][$loop] = $url_key;
            $meta_title = $name;
            $meta_keywords = '';
            $meta_description = $this->metatitledec($deviceName,$planName,$data['price'],$loop);
            $dt = new \DateTime();
            if($loop == 'default'){ $created_at = $dt->format('Y-m-d H:i:s'); }else {$created_at ='';}
            if($loop == 'default'){ $updated_at = $dt->format('Y-m-d H:i:s'); }else {$updated_at ='';}
            $new_from_date = '';
            $new_to_date = '';
            if($loop == 'default'){ $display_product_options_in = 'Block after Info Column'; }else {$display_product_options_in = ''; }
            $map_price = ''; 
            $msrp_price = '';
            $map_enabled = '';
            $gift_message_available = '';
            $custom_design = '';
            $custom_design_from = '';
            $custom_design_to = '';
            $custom_layout_update = '';
            $page_layout = '';
            $product_options_container = '';
            if($loop == 'default'){ $msrp_display_actual_price_type = 'Use config'; }else { $msrp_display_actual_price_type = ''; }
            $country_of_manufacture = '';
            if($loop == 'default'){ $additional_attributes = 'has_options=1,shipment_type=together,quantity_and_stock_status=In Stock,required_options=0'; }else { $additional_attributes =''; }
            if($loop == 'default'){ $qty = 0; }else { $qty = ''; }
            if($loop == 'default'){ $out_of_stock_qty = 0; }else { $out_of_stock_qty = '';}
            if($loop == 'default'){ $use_config_min_qty = 1; }else { $use_config_min_qty ='';}
            if($loop == 'default'){ $is_qty_decimal = 0; }else { $is_qty_decimal ='';}
            if($loop == 'default'){ $allow_backorders = 0; }else { $allow_backorders ='';}
            if($loop == 'default'){ $use_config_backorders = 1; }else { $use_config_backorders ='';}
            if($loop == 'default'){ $min_cart_qty = 1; }else {$min_cart_qty =''; }
            if($loop == 'default'){ $use_config_min_sale_qty = 0; }else {$use_config_min_sale_qty = ''; }
            if($loop == 'default'){ $max_cart_qty = 0; }else {$max_cart_qty = ''; }
            if($loop == 'default'){ $use_config_max_sale_qty = 1; }else {$use_config_max_sale_qty = '';}
            if($loop == 'default'){ $is_in_stock = 1; }else {$is_in_stock =''; }
            $notify_on_stock_below = '';
            if($loop == 'default'){ $use_config_notify_stock_qty = 1; }else { $use_config_notify_stock_qty= ''; }
            if($loop == 'default'){ $manage_stock = 0; }else {$manage_stock='';}
            if($loop == 'default'){ $use_config_manage_stock = 1; }else {$use_config_manage_stock ='';}
            if($loop == 'default'){ $use_config_qty_increments = 1; }else {$use_config_qty_increments ='';}
            if($loop == 'default'){ $qty_increments = 0; }else {$qty_increments = '';}
            if($loop == 'default'){ $use_config_enable_qty_inc = 1; }else {$use_config_enable_qty_inc =''; }
            if($loop == 'default'){ $enable_qty_increments = 0; }else {$enable_qty_increments =''; }
            if($loop == 'default'){ $is_decimal_divided = 0; }else {$is_decimal_divided = ''; }
            if($loop == 'default'){ $website_id = 1; }else {$website_id = ''; }
            if($loop == 'default'){ $deferred_stock_update = 0; }else { $deferred_stock_update=''; }
            if($loop == 'default'){ $use_config_deferred_stock_update = 1; }else { $use_config_deferred_stock_update ='';}
            $related_skus = '';
            $crosssell_skus = '';
            $upsell_skus = '';
            $hide_from_product_page = '';
            $custom_options = '';
            if($loop == 'default'){ $bundle_price_type = 'fixed'; }else {$bundle_price_type ='';}
            if($loop == 'default'){ $bundle_sku_type = 'fixed'; }else {$bundle_sku_type = ''; }
            if($loop == 'default'){ $bundle_price_view = 'Price range'; } else {$bundle_price_view=''; } 
            if($loop == 'default'){ $bundle_weight_type = 'fixed'; }else { $bundle_weight_type = ''; }
            if($loop == 'default'){ $bundle_values = "name=$name,type=checkbox,required=1,sku=$dsku,price=0.0000,default=1,default_qty=1.0000,price_type=fixed|name=$name,type=checkbox,required=1,sku=$vsku,price=0.0000,default=1,default_qty=1.0000,price_type=fixed"; }else{ $bundle_values = ''; }
            if($loop == 'default'){ $product_icon_image = "connection"; }else {$product_icon_image = ''; }
            if($loop == 'default'){ $shipment_type = 0; } else {$shipment_type=''; } 
            $custom_bundle = 'yes';
            $pID = $this->getPordID($dsku);
            $image = $this->getProductImagePath($pID);
            $imagebase = $this->getBaseProductImagePath($pID);
           if($loop == 'default'){   $this->imgPath[$sku]['base'] = $imagebase;  }           
            $handset_family = $this->getDetaillsFromVarCharTable($data['device_sku'], $loop, '347');
           if($loop == 'default'){  $this->imgPath[$sku]['additional_images'] = $this->additionalImage($image); }
           if($loop == 'default'){  $context = $this->getContextAttr($data['context']); }else {$context= ''; }
           if($loop == 'default'){  $bundle_delete = 'no'; }
            
            $row[$sku][$loop] = array($sku, $store_view_code, $attribute_set_code, $product_type, $categories, $product_websites, $name,
                $description, $short_description, $weight, $product_online, $tax_class_name, $visibility, $price, $soho_priceVal, $special_price,
                $special_price_from_date, $special_price_to_date, $url_key, $meta_title, $meta_keywords, $meta_description,
                $created_at, $updated_at, $new_from_date, $new_to_date, $display_product_options_in, $map_price, $msrp_price,
                $map_enabled, $gift_message_available, $custom_design, $custom_design_from, $custom_design_to, $custom_layout_update,
                $page_layout, $product_options_container, $msrp_display_actual_price_type, $country_of_manufacture,
                $additional_attributes, $qty, $out_of_stock_qty, $use_config_min_qty, $is_qty_decimal, $allow_backorders,
                $use_config_backorders, $min_cart_qty, $use_config_min_sale_qty, $max_cart_qty, $use_config_max_sale_qty,
                $is_in_stock, $notify_on_stock_below, $use_config_notify_stock_qty, $manage_stock, $use_config_manage_stock,
                $use_config_qty_increments, $qty_increments, $use_config_enable_qty_inc, $enable_qty_increments,
                $is_decimal_divided, $website_id, $deferred_stock_update, $use_config_deferred_stock_update, $related_skus,
                $crosssell_skus, $upsell_skus, $hide_from_product_page, $custom_options, $bundle_price_type, $bundle_sku_type,
                $bundle_price_view, $bundle_weight_type, $bundle_values, $product_icon_image, $shipment_type,$custom_bundle,$handset_family,
                $context,$bundle_delete               
            );
        }
        return $row;
        }else{
            $this->messageManager->addError('Error in row value '.$data['device_sku']. ' OR '.$data['plan_sku']);
        }
        }else{
            $this->messageManager->addError('Error in File');			 
        }       
        
    }
    
	public function getProductStatusInfor($devicesku){
		$deviceRowID = $this->getPordID($devicesku);
		if($deviceRowID){
			$sql = 'SELECT value FROM catalog_product_entity_int where attribute_id = 97 AND row_id ='.$deviceRowID.' AND store_id = 0';
            $reData = $this->_resourceConnection->fetchRow($sql);
			if(isset($reData['value'])){
				return $reData['value'];				
			}else{
				return 1;
			}
		}
	}
    
    public function metatitledec($deviceName, $planName, $price, $store) {
        if ($store == 'nl') {
            $desc = 'Koop je ' . $deviceName . ' met ' . $planName . ' aan ' . $price;
        } elseif ($store == 'fr') {
            $desc = 'Acheter votre ' . $deviceName . ' avec ' . $planName . ' à ' . $price;
        } else {
            $desc = 'Acheter votre ' . $deviceName . ' avec ' . $planName . ' à ' . $price;
        }
        return $desc;
    }

    /*
     * Based on this attribute product shown for particular customer group
     */
    public function getContextAttr($context) {
        if ($context == 'soho') {
            $cont = 'SOHO';
        } else if ($context == 'b2c') {
            $cont = 'B2C';
        } else {
            $cont = 'ALL CONTEXT';
        }
        return $cont;
    }

    /*
     * Add additional images in sheet
     */
    public function additionalImage($image){     
        //$images = array_shift($image);
        $imageAppend = array();
        foreach($image as $img){
            $imageAppend[] = $img;            
        }       
        //return implode(',',$imageAppend);
        return $imageAppend;
    }


    /*
     * Fetch Values from Varchar table
     */
    public function getDetaillsFromVarCharTable($sku, $store_id, $attrid) {
        if ($store_id == 'nl') {
            $store = 1;
        } elseif ($store_id == 'fr') {
            $store = 2;
        } else {
            $store = 0;
        }
        $sql = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $sku . "' and ev.attribute_id='" . $attrid . "' AND e.row_id = ev.row_id AND ev.store_id ='" . $store . "'";
        $data = $this->_resourceConnection->fetchRow($sql);
        $checked = $this->validategetDetaillsFromVarCharTable($sku, $data, $attrid);
        return $checked;
    }
    
    public function getBaseProductImagePath($id){        
         $sql = "SELECT value FROM catalog_product_entity_varchar where row_id =' ".$id."' AND attribute_id = '87'";
         $reData = $this->_resourceConnection->fetchRow($sql);
         return $reData['value'];
    }

    public function validategetDetaillsFromVarCharTable($sku, $data, $attrid) {
        if ($data['value'] == '') {
             $sql = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $sku . "' and ev.attribute_id='" . $attrid . "' AND 
						e.row_id = ev.row_id AND ev.store_id ='0'";
             $reData = $this->_resourceConnection->fetchRow($sql);
        } else {
            $reData = $data;
        }
        return $reData['value'];
    }
    
    /*
     * Fetch Values from Text Table
     */
    public function getDetaillsFromTextTable($sku, $store_id, $attrid) {

        if ($store_id == 'nl') {
            $store = 1;
        } elseif ($store_id == 'fr') {
            $store = 2;
        } else {
            $store = 0;
        }
        $sql = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $sku . "' and ev.attribute_id='" . $attrid . "' AND 
						e.row_id = ev.row_id AND ev.store_id ='" . $store . "'";
        $data = $this->_resourceConnection->fetchRow($sql);
        $checkedDta = $this->validategetDetaillsFromTextTable($sku, $data, $attrid);
        return $checkedDta;
    }
    
     public function validategetDetaillsFromTextTable($sku, $data, $attrid) {
        if ($data['value'] == '') {
            $sql = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $sku . "' and ev.attribute_id='" . $attrid . "' AND 
						e.row_id = ev.row_id AND ev.store_id ='0'";
            $reData = $this->_resourceConnection->fetchRow($sql);
        } else {
            $reData = $data;
        }
        return $reData['value'];
    }

    /*
     * Fetch Data For URL in varchar Table
     */

    public function getDetaillsFromVarCharTableURL($dsku, $vsku, $store_id, $attrid) {

        $sql = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $dsku . "' and ev.attribute_id='" . $attrid . "' AND e.row_id = ev.row_id AND ev.store_id = 0 LIMIT 1";
        $sql1 = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $vsku . "' and ev.attribute_id='" . $attrid . "' AND  e.row_id = ev.row_id AND ev.store_id = 0 LIMIT 1";

        $data = $this->_resourceConnection->fetchRow($sql);
        $data1 = $this->_resourceConnection->fetchRow($sql1);
        $vituralSku = '';
        if ($data1['value']) {
            if (strpos($data1['value'], '-orange') !== false) {
                $vituralSku = str_replace('-orange', '', $data1['value']);
            } else {
                $vituralSku = $data1['value'];
            }
        }
        if ($store_id == 'default') {
            $durl = $data['value'];
            $vurl = $vituralSku;
        } else if ($store_id == 'fr') {
            $sql2 = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $dsku . "' and ev.attribute_id='" . $attrid . "' AND e.row_id = ev.row_id AND ev.store_id = 2 LIMIT 1";
            $sql21 = "SELECT ev.value FROM catalog_product_entity e INNER JOIN catalog_product_entity_varchar ev ON e.sku ='" . $vsku . "' and ev.attribute_id='" . $attrid . "' AND  e.row_id = ev.row_id AND ev.store_id = 2 LIMIT 1";

            $data2 = $this->_resourceConnection->fetchRow($sql2);
            $data21 = $this->_resourceConnection->fetchRow($sql21);

            if ($data2['value'] == '') {
                $durl = $data['value'];
            } else {
                $durl = $data2['value'];
            }

            if ($data21['value'] == '') {
                $vurl = $vituralSku;
            } else {
                if (strpos($data21['value'], '-orange') !== false) {
                    $vituralSku = str_replace('-orange', '', $data21['value']);
                } else {
                    $vituralSku = $data21['value'];
            }

                $vurl = $vituralSku;
            }
        } else {
            $durl = '';
            $vurl = '';
        }
        $name = preg_replace('#[^0-9a-z]+#i', '-', $durl . '+' . $vurl);
        return $name;
    }

    public function validategetDetaillsFromVarCharTableURL($dsku, $vsku, $data, $data1, $store_id) {               
        $reData = array();       
        if ($data['value'] == '' || $data1['value'] == '') {
            $reData['device'] = '';
            $reData['sub'] = '';
        } else {
            $reData['device'] = $data['value'];
            $reData['sub'] = $data['value'];
        }
        return $reData;
    }

    /*
     * Get Product ID From entity table
     */
    public function getPordID($sku) {
        $entitySql = "SELECT row_id FROM catalog_product_entity WHERE sku ='" . $sku . "' LIMIT 1";
        $entityId = $this->_resourceConnection->fetchRow($entitySql);
        return $entityId['row_id'];
    }
    
    /*
     * Get Simple Product Image
     */
    public function getProductImagePath($pID) {

        $imgSql = "SELECT value FROM catalog_product_entity_media_gallery img INNER JOIN catalog_product_entity_media_gallery_value imgv ON imgv.row_id = '" . $pID . "' and imgv.value_id=img.value_id and imgv.store_id = 0;";
        $image = array();
        $imgsList = $this->_resourceConnection->fetchAll($imgSql);

        if (count($imgsList)) {
            $image = array();
            foreach ($imgsList as $img) {
                $image[] = $img['value'];
            }
            return $image;
        }
        return $image;
    }

}
