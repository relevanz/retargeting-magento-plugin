<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 5:19 PM
 */
namespace Relevanz\Tracking\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED      = 'relevanz_tracking/settings/enabled';
    const XML_PATH_CLIENT_ID    = 'relevanz_tracking/settings/client_id';
    const XML_PATH_API_KEY      = 'relevanz_tracking/settings/api_key';

    const XML_PATH_TRACKING_FRONT_PAGE      = 'relevanz_tracking/tracking/front_page_enabled';
    const XML_PATH_TRACKING_FRONT_CATEGORY  = 'relevanz_tracking/tracking/category_page_enabled';
    const XML_PATH_TRACKING_FRONT_PRODUCT   = 'relevanz_tracking/tracking/product_page_enabled';
    const XML_PATH_TRACKING_SUCCESS_PAGE    = 'relevanz_tracking/tracking/order_success_page_enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_scopeConfig     = $this->scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(){
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getClientId(){
        return (string)$this->_scopeConfig->getValue(self::XML_PATH_CLIENT_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function isFrontPageTrackEnabled(){
        return (string)$this->_scopeConfig->getValue(self::XML_PATH_TRACKING_FRONT_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function isCategoryTrackEnabled(){
        return (string)$this->_scopeConfig->getValue(self::XML_PATH_TRACKING_FRONT_CATEGORY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function isProductTrackEnabled(){
        return (string)$this->_scopeConfig->getValue(self::XML_PATH_TRACKING_FRONT_PRODUCT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function isSuccessPageTrackEnabled(){
        return (string)$this->_scopeConfig->getValue(self::XML_PATH_TRACKING_SUCCESS_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}