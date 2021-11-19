<?php
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
use Magento\Config\Model\ResourceModel\Config;
use Magento\Backend\App\Action\Context;

class KeyPost extends Statistics
{

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    private $resourceConfig;
    
    private $cacheTypeList;
    
    private $dataHelper;

    /**
     * @param Config $resourceConfig
     * @paran TypeListInterface $cacheTypeList
     * @param Context $context
     */
    public function __construct(
        \Relevanz\Tracking\Helper\Data $dataHelper,
        Config $resourceConfig,
        TypeListInterface $cacheTypeList,
        Context $context
    ) {
        $this->dataHelper = $dataHelper;
        $this->cacheTypeList = $cacheTypeList;
        $this->resourceConfig = $resourceConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $storeId    =   $this->getRequest()->getParam('store', 0);
        $apiKey     =   $this->getRequest()->getPostValue('api_key');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            /*$clientId = *///@todo save to config?
            \Releva\Retargeting\Base\RelevanzApi::verifyApiKey($apiKey, [
                'callback-url' => $this->dataHelper->getShopInfo()['callbacks']['callback']['url'],
            ]);
            $scope = ($storeId) ? \Magento\Store\Model\ScopeInterface::SCOPE_STORES : \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $this->resourceConfig->saveConfig(\Relevanz\Tracking\Helper\Data::XML_PATH_API_KEY, $apiKey, $scope, $storeId);
            $this->messageManager->addSuccess(__('You have successfully added your configuration!'));
            $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        } catch (\Exception $e) {//@todo catch relevanz-exceptions
            $this->messageManager->addError(__($e->getMessage()));
        }

        $params = array();
        if($storeId){
            $params['store'] = $storeId;
        }
        $resultRedirect->setPath('*/*/index', $params);
        return $resultRedirect;

    }
}