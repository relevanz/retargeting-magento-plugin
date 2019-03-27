<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/26/17
 * Time: 11:38 AM
 */
namespace Extensions\RelevaTracking\Controller\Adminhtml\Statistics;

use Magento\Framework\Controller\ResultFactory;

class KeyValidation extends \Extensions\RelevaTracking\Controller\Adminhtml\Statistics
{
    /**
     * @var \Extensions\RelevaTracking\Model\Api
     */
    protected $_api;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Extensions\RelevaTracking\Model\Api $api
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Extensions\RelevaTracking\Model\Api $api
    ) {
        $this->_coreRegistry        =   $coreRegistry;
        $this->resultForwardFactory =   $resultForwardFactory;
        $this->resultPageFactory    =   $resultPageFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_api     =   $api;
    }

    /**
     * Get JSON data
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $apiKey     =   $this->getRequest()->getParam('api_key', null);
        $response   =   $this->_api->getUser($apiKey);
        if($response->getStatus() == 'success'){
            $result = json_decode($response->getResult());
            $data = array(
                'status' => $response->getStatus(),
                'values' => $result
            );
        }
        else{
            $data = $response->convertToArray();
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
}