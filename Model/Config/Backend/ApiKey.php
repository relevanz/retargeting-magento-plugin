<?php
namespace Relevanz\Tracking\Model\Config\Backend;

class ApiKey extends \Magento\Framework\App\Config\Value {
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;
    
    private $dataHelper;

    public function __construct(
        \Relevanz\Tracking\Helper\Data $dataHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    public function beforeSave()
    {
        $this->dataHelper->verifyApiKeyAndDisplayErrors($this->getValue());
        parent::beforeSave();
    }
}