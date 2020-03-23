<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 6:11 PM
 */

namespace Relevanz\Tracking\Block\Adminhtml\Statistics;

use Relevanz\Tracking\Helper\Data as Helper;
use Relevanz\Tracking\Model\Api as RelevanzApi;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;

class View extends Widget {

    private $helper;
    
    /**
     * @var RelevanzApi
     */
    private $relevanzApi;

    protected $_template = 'Relevanz_Tracking::statistics/view.phtml';

    /**
     * @param Helper $helper
     * @param RelevanzApi $relevanzApi
     * @param Context $context
     * @param array $data
     */
    public function __construct(Helper $helper, RelevanzApi $relevanzApi, Context $context, array $data = [])
    {
        $this->helper = $helper;
        $this->relevanzApi = $relevanzApi;
        parent::__construct($context, $data);
    }
    
    /**
     * @return bool
     */
    public function isStore()
    {
        return $this->_getStoreId() !== 0;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store', 0);
    }
    
    public function getApiKey()
    {
        return $this->helper->getApiKey();
    }

    /**
     * @return bool
     */
    public function validateApiKey()
    {
        $apiKey = $this->getApiKey();
        if(!$apiKey){
            return false;
        }
        try {
            $validateApiKey = $this->relevanzApi->getUser($apiKey);
            if($validateApiKey->getStatus() != 'success'){
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
    
    /**
     * Api Key Post Url
     * @return string
     */
    public function apiKeyPostUrl(){
        $params = array();
        if($storeId = $this->_getStoreId()) {
            $params['store'] = $storeId;
        }
        return $this->getUrl('*/*/keyPost', $params);
    }

}