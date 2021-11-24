<?php declare(strict_types = 1);
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/27/17
 * Time: 11:51 AM
 */
namespace Relevanz\Tracking\Controller\Adminhtml\Statistics;

use Relevanz\Tracking\Controller\Adminhtml\Statistics;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Relevanz\Tracking\Helper\Data as DataHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\Type\Config as CacheTypeConfig;

class KeyPost extends Statistics
{
    
    private $resourceConfig;
    
    private $cacheTypeList;
    
    private $dataHelper;

    public function __construct(
        DataHelper $dataHelper,
        ResourceConfig $resourceConfig,
        TypeListInterface $cacheTypeList,
        Context $context
    ) {
        $this->dataHelper = $dataHelper;
        $this->cacheTypeList = $cacheTypeList;
        $this->resourceConfig = $resourceConfig;
        parent::__construct($context);
    }
    
    public function execute() : Redirect
    {
        $storeId    =   $this->getRequest()->getParam('store', 0);
        $apiKey     =   $this->getRequest()->getPostValue('api_key');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->dataHelper->verifyApiKeyAndDisplayErrors($apiKey) !== null) {
            $scope = ($storeId) ? ScopeInterface::SCOPE_STORES : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $this->resourceConfig->saveConfig(DataHelper::XML_PATH_API_KEY, $apiKey, $scope, $storeId);
            $this->cacheTypeList->cleanType(CacheTypeConfig::TYPE_IDENTIFIER);
            $this->messageManager->addSuccess(__('You saved the configuration.'));
        }
        $params = array();
        if($storeId){
            $params['store'] = $storeId;
        }
        $resultRedirect->setPath('*/*/index', $params);
        return $resultRedirect;
    }
}