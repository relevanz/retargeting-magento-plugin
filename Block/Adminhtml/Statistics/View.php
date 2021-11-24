<?php declare(strict_types = 1);
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 6:11 PM
 */

namespace Relevanz\Tracking\Block\Adminhtml\Statistics;

use Releva\Retargeting\Base\RelevanzApi;
use Relevanz\Tracking\Helper\Data as Helper;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;

class View extends Widget {

    private $helper;
    
    protected $_template = 'Relevanz_Tracking::statistics/view.phtml';
    
    public function __construct(Helper $helper, Context $context, array $data = [])
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }
    
    public function isStore() : bool
    {
        return $this->_getStoreId() !== 0;
    }
    
    protected function _getStoreId() : int
    {
        return (int)$this->getRequest()->getParam('store', 0);
    }
    
    public function getApiKey() : string
    {
        return $this->helper->getApiKey();
    }
    
    public function getIframeUrl() : string
    {
        return $this->isStore() && $this->validateApiKey()
            ? RelevanzApi::RELEVANZ_STATS_FRAME.$this->helper->getApiKey()
            : 'https://releva.nz'
        ;
    }
    
    public function validateApiKey() : bool
    {
        try {
            RelevanzApi::verifyApiKey($this->getApiKey());
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    public function apiKeyPostUrl() : string
    {
        $params = array();
        if($storeId = $this->_getStoreId()) {
            $params['store'] = $storeId;
        }
        return $this->getUrl('*/*/keyPost', $params);
    }

}