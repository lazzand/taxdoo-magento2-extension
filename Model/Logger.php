<?php
/**
 * Taxdoo_VAT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Taxdoo
 * @package    Taxdoo_VAT
 * @copyright  Copyright (c) 2017 TaxJar. TaxJar is a trademark of TPS Unlimited, Inc. (http://www.taxjar.com)
 * @copyright  Copyright (c) 2021 Andrea Lazzaretti.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Taxdoo\VAT\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Taxdoo\VAT\Model\Configuration as TaxdooConfig;
use Magento\Framework\Filesystem\Driver;

class Logger
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var array
     */
    protected $playback = [];

    /**
     * @var bool
     */
    protected $isRecording;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $console;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $filename = TaxdooConfig::TAXDOO_DEFAULT_LOG;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TaxdooConfig
     */
    protected $taxdooConfig;

    /**
     * @var boolean
     */
    protected $isForced = false;

    /**
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        TaxdooConfig $taxdooConfig
    ) {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->taxdooConfig = $taxdooConfig;
    }

    /**
     * Sets the log filename
     *
     * @param string $filename
     * @return Logger
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Enables the logger
     *
     * @param boolean $isForced
     * @return Logger
     */
    public function force()
    {
        $this->isForced = true;
        return $this;
    }

    /**
     * Enables the logger
     *
     * @param boolean $isForced
     * @return Logger
     */
    public function unForce()
    {
        $this->isForced = false;
        return $this;
    }

    /**
     * Get the temp log filename
     *
     * @return string
     */
    public function getPath()
    {
        return ($this->directoryList->getPath(DirectoryList::LOG)
                . DIRECTORY_SEPARATOR
                . 'taxdoo'
                . DIRECTORY_SEPARATOR
                . $this->filename);
    }

    /**
     * Save a message to taxdoo.log
     *
     * @param string $message
     * @param string $label
     * @throws LocalizedException
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function log($message, $label = '')
    {
        if ($this->scopeConfig->getValue(
            TaxdooConfig::TAXDOO_DEBUG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        ) || $this->isForced
        ) {
            try {
                if (!empty($label)) {
                    $label = '[' . strtoupper($label) . '] ';
                }

                if ($this->taxdooConfig->isSandboxEnabled()) {
                    $label = '[SANDBOX] ' . $label;
                }

                $timestamp = date('d M Y H:i:s', time());
                $message = sprintf('%s%s - %s%s', PHP_EOL, $timestamp, $label, $message);

                if (!$this->driverFile->isDirectory($this->driverFile->getParentDirectory($this->getPath()))) {
                    // dir doesn't exist, make it
                    $this->driverFile->createDirectory($this->driverFile->getParentDirectory($this->getPath()));
                }

                $this->driverFile->filePutContents($this->getPath(), $message, FILE_APPEND);

                if ($this->isRecording) {
                    $this->playback[] = $message;
                }
                if ($this->console) {
                    $this->console->write($message);
                }
            } catch (\Exception $e) {
                // @codingStandardsIgnoreStart
                throw new LocalizedException(__('Could not write to your Magento log directory under /var/log. Please make sure the directory is created and check permissions for %1.', $this->directoryList->getPath('log')));
                // @codingStandardsIgnoreEnd
            }
        }
    }

    /**
     * Enable log recording
     *
     * @return void
     */
    public function record()
    {
        $this->isRecording = true;
    }

    /**
     * Return log recording
     *
     * @return array
     */
    public function playback()
    {
        return $this->playback;
    }

    /**
     * Set console output interface
     *
     * @return void
     */
    public function console($output)
    {
        $this->console = $output;
    }
}
