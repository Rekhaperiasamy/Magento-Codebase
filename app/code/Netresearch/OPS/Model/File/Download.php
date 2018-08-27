<?php
namespace Netresearch\OPS\Model\File;

class Download
{
    /**
     * Number of bytes in one MB
     */
    const ONE_MEGABYTE = 1048576;

    /**
     * @var resource
     */
    protected $_fileHandler;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * Takes a file name and a size threshold.
     * If the file is bigger than the threshold, the last written lines, up to the size of the
     * threshold are returned
     *
     * @param string $path Path to file on server
     * @param int $threshold Max file size, default is 1MB
     * @return string Path to file to offer to download
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFile($path, $threshold = self::ONE_MEGABYTE)
    {
        $this->_filePath = $path;
        if (!file_exists($this->_filePath) || !is_readable($this->_filePath)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File ' . $this->_filePath . ' does not exist or is not readable.')
            );
        }
        if (filesize($path) > $threshold) {
            return $this->_trimFileToThreshold($threshold);
        } else {
            return $this->_filePath;
        }
    }

    /**
     * Trims the current file to the given threshold and creates a temporary file
     *
     * @param int $threshold Max file size
     * @return string Path to the temporary file
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _trimFileToThreshold($threshold)
    {
        $this->_fileHandler = fopen($this->_filePath, 'r');
        if (0 > fseek($this->_fileHandler, filesize($this->_filePath)-$threshold, SEEK_SET)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Function fseek on file '. $this->_filePath.' failed.')
            );
        }
        $content = fread($this->_fileHandler, $threshold);
        fclose($this->_fileHandler);
        $tempFileName = tempnam(\Netresearch\OPS\Helper\Data::getPathToLogFile(), 'tempFile');
        file_put_contents($tempFileName, $content);

        return $tempFileName;
    }
}
