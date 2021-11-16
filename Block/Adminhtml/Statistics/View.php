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
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;

class View extends Widget {

    private $helper;
    
    protected $_template = 'Relevanz_Tracking::statistics/view.phtml';

    /**
     * @param Helper $helper
     * @param Context $context
     * @param array $data
     */
    public function __construct(Helper $helper, Context $context, array $data = [])
    {
        $this->helper = $helper;
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
    
    public function getIframeUrl()
    {
        return $this->isStore() && $this->validateApiKey()
            ? \Releva\Retargeting\Base\RelevanzApi::RELEVANZ_STATS_FRAME.$this->helper->getApiKey()
            : 'https://releva.nz'
        ;
    }

    /**
     * @return bool
     */
    public function validateApiKey()
    {
        try {
            \Releva\Retargeting\Base\RelevanzApi::verifyApiKey($this->getApiKey());
            return true;
        } catch (\Exception $ex) {
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