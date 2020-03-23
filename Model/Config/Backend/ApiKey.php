<?php
namespace Relevanz\Tracking\Model\Config\Backend;

class ApiKey extends \Magento\Framework\App\Config\Value {
    
    private $relevanzApi;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(
        \Relevanz\Tracking\Model\Api $relevanzApi,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->relevanzApi = $relevanzApi;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    public function beforeSave()
    {
        try {
            $this->relevanzApi->getUser($this->getValue());
        } catch (\Exception $exception) {
            $this->messageManager->addError(__($exception->getMessage()));
        }
        parent::beforeSave();
    }
}