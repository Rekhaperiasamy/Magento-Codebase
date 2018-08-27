<?php

namespace Orange\Abandonexport\Controller\Adminhtml\Items;


class Download extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $file = $this->getRequest()->getParam('file');
		$file = BP.'/common-header/abandonedreport/'.$file.'.csv';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Encoding: UTF-8');
			header('Content-Type: application/csv;charset=UTF-8');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			readfile($file);
		}else{
			echo 'File not Exist';
			exit;
		}
	}
}

?>