<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/26/17
 * Time: 5:56 PM
 */
namespace Relevanz\Tracking\Helper\Admin;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper {

    const XML_PATH_CLIENT_ID    = 'relevanz_tracking/settings/client_id';
    const XML_PATH_API_KEY      = 'relevanz_tracking/settings/api_key';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     */
    public function __construct(StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig, Context $context)
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getApiKey($storeId = 0){
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_KEY, 
            ScopeInterface::SCOPE_STORES,
            $this->storeManager->getStore($storeId)
        );
    }

}