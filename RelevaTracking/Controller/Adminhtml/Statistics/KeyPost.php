<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/27/17
 * Time: 11:51 AM
 */
namespace Extensions\RelevaTracking\Controller\Adminhtml\Statistics;

use Magento\Framework\Controller\ResultFactory;

class KeyPost extends \Extensions\RelevaTracking\Controller\Adminhtml\Statistics
{
    /**
     * @var \Extensions\RelevaTracking\Model\Api
     */
    protected $_api;

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $_resourceConfig;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Extensions\RelevaTracking\Model\Api $api
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Extensions\RelevaTracking\Model\Api $api
    ) {
        $this->_coreRegistry        =   $coreRegistry;
        $this->resultForwardFactory =   $resultForwardFactory;
        $this->resultPageFactory    =   $resultPageFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_api             =   $api;
        $this->_resourceConfig  =   $resourceConfig;
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
            $response   =   $this->_api->getUser($apiKey);
            if ($response->getStatus() == 'success') {
                $result = json_decode($response->getResult());
                if(isset($result->user_id) && ($clientId = $result->user_id)) {
                    $scope = ($storeId) ? \Magento\Store\Model\ScopeInterface::SCOPE_STORES : \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
                    $this->_resourceConfig->saveConfig(
                        \Extensions\RelevaTracking\Helper\Admin\Data::XML_PATH_CLIENT_ID,
                        $clientId,
                        $scope,
                        $storeId
                    );
                    $this->_resourceConfig->saveConfig(
                        \Extensions\RelevaTracking\Helper\Admin\Data::XML_PATH_API_KEY,
                        $apiKey,
                        $scope,
                        $storeId
                    );
                    $this->messageManager->addSuccess(__('You have successfully added your configuration!'));
                }
            } else {
                $this->messageManager->addError(__($response->getMessage()));
            }
        } catch (\Exception $e) {
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