<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/26/17
 * Time: 11:35 AM
 */
namespace Relevanz\Tracking\Controller\Adminhtml\Statistics;

use Magento\Framework\Controller\ResultFactory;

class Data extends \Relevanz\Tracking\Controller\Adminhtml\Statistics
{
    /**
     * @var \Relevanz\Tracking\Model\Api
     */
    protected $_api;

    /**
     * @var \Relevanz\Tracking\Helper\Admin\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Relevanz\Tracking\Model\Api $api
     * @param \Relevanz\Tracking\Helper\Admin\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Relevanz\Tracking\Model\Api $api,
        \Relevanz\Tracking\Helper\Admin\Data $helper
    ) {
        $this->_coreRegistry        =   $coreRegistry;
        $this->resultForwardFactory =   $resultForwardFactory;
        $this->resultPageFactory    =   $resultPageFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_api     =   $api;
        $this->_helper  =   $helper;
    }

    /**
     * Get JSON data
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $storeId    =   $this->getRequest()->getParam('store', 0);
        $reportFrom =   $this->getRequest()->getParam('report_from', 0);
        $reportTo   =   $this->getRequest()->getParam('report_to', 0);
        $apiKey     =   $this->_helper->getApiKey($storeId);
        $response   =   $this->_api->getStatisticData($apiKey, $reportFrom, $reportTo);

        if($response->getStatus() == 'success'){
            $result = json_decode($response->getResult());
            $data = array(
                'status' => $response->getStatus(),
                'values' => (isset($result->query_result->data->rows)) ? $result->query_result->data->rows : array()
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