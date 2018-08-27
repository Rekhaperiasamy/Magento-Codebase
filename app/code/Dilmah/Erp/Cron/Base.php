<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Erp
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Erp\Cron;

class Base
{
    /**
     * xml path for fpt server name
     */
    const XML_PATH_FTP_SERVER = 'erp/ftp/host';

    /**
     * xml path for ftp username
     */
    const XML_PATH_FTP_USER = 'erp/ftp/server_user';

    /**
     * xml path for ftp user password
     */
    const XML_PATH_FTP_PASSWORD = 'erp/ftp/server_password';

    /**
     * Storage key for SFTP
     */
    const SFTP_STORAGE = 'sftp';

    /**
     * order data upload directory
     */
    const FTP_ORDER_OUTWARD_DIR = 'Inbound';
	
	const FTP_ORDER_MJFERP_DIR = 'MJFERP';

    /**
     * order data download directory
     */
    const FTP_ORDER_INWARD_DIR = 'Outbound/Status';

    /**
     * inventory data download directory
     */
    const FTP_INVENTORY_DIR = 'Outbound/Inventory';

    /**
     * directory to move read files
     */
    const FTP_ARCHIVE_DIR = 'Archive';

    /**
     * website id of the Main Website
     */
    const GLOBAL_WEBSITE_ID = 1;

    /**
     * @var array
     */
    protected $fileInfo = [];

    /**
     * Filesystem instance
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\Ftp
     */
    protected $sftpAdapter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Dilmah\Erp\Logger\Logger
     */
    protected $logger;

    /**
     * Base constructor
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Dilmah\Erp\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Dilmah\Erp\Logger\Logger $logger
    ) {
        $this->filesystem = $filesystem;
        $this->sftpAdapter = $sftpAdapter;
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resource;
        $this->logger = $logger;

        $this->fileInfo = [
            'host' => $this->scopeConfig->getValue(self::XML_PATH_FTP_SERVER),
            'username' => $this->scopeConfig->getValue(self::XML_PATH_FTP_USER),
            'password' => $this->scopeConfig->getValue(self::XML_PATH_FTP_PASSWORD),
        ];
    }

    /**
     * Write data to specific storage (FTP, local filesystem)
     *
     * @param string $fileContent
     * @return bool
     */
    protected function writeData($fileContent)
    {
        $fileInfo = $this->getFileInfo();
        $fileName = $fileInfo['file_path'] . '/' . $fileInfo['file_name'] . '.' . $fileInfo['file_format'];

        try {
            // write file in local file system
            $rootDirectory = $this->filesystem->getDirectoryWrite(
                \Magento\Framework\App\Filesystem\DirectoryList::ROOT
            );
            $rootDirectory->writeFile('/dilmah_erp/' . $fileName, $fileContent);

            /**
             * To prevent below error
             * "Methods with the same name as their class will not be constructors in a future version of PHP;
             * Net_SFTP has a deprecated constructor"
             */
            error_reporting(0);

            // write file to sftp server
            $this->sftpAdapter->open($this->_prepareIoConfiguration($fileInfo));
            $fileName = trim($fileName, '\\/');
            $result = $this->sftpAdapter->write($fileName, $fileContent);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * read data from SFTP storage (FTP, local filesystem)
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function readData()
    {
        $fileInfo = $this->getFileInfo();
        $fileName = $fileInfo['file_path'] . '/' . $fileInfo['file_name'];

        /**
         * To prevent below error
         * "Methods with the same name as their class will not be constructors in a future version of PHP;
         * Net_SFTP has a deprecated constructor"
         */
        error_reporting(0);

        $this->sftpAdapter->open($this->_prepareIoConfiguration($fileInfo));
        $fileName = trim($fileName, '\\/');
        $result = $this->sftpAdapter->read($fileName);

        $this->logger->info(__('Reading file "%1".', $fileName));

        if (!$result) {
            $this->logger->error(__('We can\'t read the file "%1".', $fileName));
        } else {
            $destination = trim(
                $fileInfo['file_path'] . '/' . self::FTP_ARCHIVE_DIR . '/' .
                $fileInfo['file_name'] . '.' . date('YmdHis'),
                '\\/'
            );
            // Moving the file into the archive folder after processing with postfixing the date to the file name
            $this->sftpAdapter->mv($fileName, $destination);
            $this->logger->info(__('File "%1" read and archived.', $fileName));
        }

        return $result;
    }

    /**
     * return the list of file names in a give directory
     *
     * @param string $dir
     * @return mixed
     */
    protected function getFileList($dir)
    {
        $fileInfo = $this->getFileInfo();
        $this->sftpAdapter->open($this->_prepareIoConfiguration($fileInfo));

        $this->sftpAdapter->cd($dir);
        $fileList = $this->sftpAdapter->rawls();

        foreach ($fileList as $key => $statusFile) {
            if ($statusFile['type'] != 1) {
                unset($fileList[$key]);
            }
        }
        $this->logger->info(__('Files to be read: [%1]', implode(', ', array_keys($fileList))));
        return $fileList;
    }

    /**
     * return the file info array
     *
     * @return array
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Prepare data for server io driver initialization
     *
     * @param array $fileInfo
     * @return array Prepared configuration
     */
    protected function _prepareIoConfiguration($fileInfo)
    {
        $data = [];
        foreach ($fileInfo as $key => &$v) {
            $key = str_replace('file_', '', $key);
            $data[$key] = $v;
        }
        unset($data['format'], $data['server_type'], $data['name']);
        if (isset($data['mode'])) {
            $data['file_mode'] = $data['mode'];
            unset($data['mode']);
        }
        if (isset($data['host']) && strpos($data['host'], ':') !== false) {
            $tmp = explode(':', $data['host']);
            $data['port'] = array_pop($tmp);
            $data['host'] = join(':', $tmp);
        }

        return $data;
    }
}
