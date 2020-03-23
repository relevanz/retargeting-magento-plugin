<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 5:19 PM
 */
namespace Relevanz\Tracking\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED      = 'relevanz_tracking/settings/enabled';
    const XML_PATH_CLIENT_ID    = 'relevanz_tracking/settings/client_id';
    const XML_PATH_API_KEY      = 'relevanz_tracking/settings/api_key';

    const XML_PATH_TRACKING_FRONT_PAGE      = 'relevanz_tracking/tracking/front_page_enabled';
    const XML_PATH_TRACKING_FRONT_CATEGORY  = 'relevanz_tracking/tracking/category_page_enabled';
    const XML_PATH_TRACKING_FRONT_PRODUCT   = 'relevanz_tracking/tracking/product_page_enabled';
    const XML_PATH_TRACKING_FRONT_SUCCESS_PAGE    = 'relevanz_tracking/tracking/order_success_page_enabled';

    private $state;
    
    private $request;
    
    private $storeManager;
    
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }
    
    private function getStoreId() {
        return 
            $this->state->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
            ? (int) $this->request->getParam('store', 0)
            : (int) $this->storeManager->getStore()->getId()
        ;
    }
    
    private function getConfigValue ($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORES, $this->getStoreId());
    }
    
    public function isAuthed($auth = '') {
        return 
            $this->state->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE // admin
            || (
                $this->isEnabled()
                && md5($this->getConfigValue(self::XML_PATH_API_KEY).':'.((string) $this->getClientId())) == $auth
            )
        ;
    }

    /**
     * @return bool
     */
    public function isEnabled () {
        return (bool) $this->getConfigValue(self::XML_PATH_ENABLED);
    }

    /**
     * @return string
     */
    public function getClientId(){
        return (string) $this->getConfigValue(self::XML_PATH_CLIENT_ID);
    }

    /**
     * @return string
     */
    public function getApiKey(){
        return (string) $this->getConfigValue(self::XML_PATH_API_KEY);
    }

    /**
     * @return string
     */
    public function isFrontPageTrackEnabled(){
        return (string) $this->getConfigValue(self::XML_PATH_TRACKING_FRONT_PAGE);
    }

    /**
     * @return string
     */
    public function isCategoryTrackEnabled(){
        return (string) $this->getConfigValue(self::XML_PATH_TRACKING_FRONT_CATEGORY);
    }

    /**
     * @return string
     */
    public function isProductTrackEnabled(){
        return (string) $this->getConfigValue(self::XML_PATH_TRACKING_FRONT_PRODUCT);
    }

    /**
     * @return string
     */
    public function isSuccessPageTrackEnabled(){
        return (string) $this->getConfigValue(self::XML_PATH_TRACKING_FRONT_SUCCESS_PAGE);
    }
    
}