<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Data\ProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Encrypted extends Value implements ProcessorInterface
{
    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param EncryptorInterface $encryptor
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Magic method called during class serialization
     *
     * @return string[]
     */
    public function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, ['_encryptor']);
    }

    /**
     * Magic method called during class un-serialization
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $this->_encryptor = ObjectManager::getInstance()->get(
            EncryptorInterface::class
        );
    }

    /**
     * Decrypt value after loading
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = $this->_encryptor->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }


    /**
     * Encrypt value before saving
     *
     * @return void
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();
        // don't save value, if an obscured value was received. This indicates that data was not changed.
        if (!empty($value) && str_starts_with($value, 'xoxb-')) {
            $this->_dataSaveAllowed = true;
            $encrypted = $this->_encryptor->encrypt($value);
            $this->setValue($encrypted);
        } elseif (empty($value)) {
            $this->_dataSaveAllowed = true;
        }
    }

    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $this->_encryptor->decrypt($value);
    }
}
