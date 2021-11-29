<?php declare(strict_types = 1);

namespace Relevanz\Tracking\Model\Config\Backend;

use Magento\Framework\App\Config\Value as ConfigValue;
use Relevanz\Tracking\Helper\Data as DataHelper;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class ApiKey extends ConfigValue
{
    
    private $dataHelper;

    public function __construct(DataHelper $dataHelper, Context $context, Registry $registry, ScopeConfigInterface $config, TypeListInterface $cacheTypeList, AbstractResource $resource = null, AbstractDb $resourceCollection = null, array $data = [])
    {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    public function beforeSave() : void
    {
        $this->dataHelper->verifyApiKeyAndDisplayErrors($this->getValue());
        parent::beforeSave();
    }
}