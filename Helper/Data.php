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
    
    private $messageManager;
    
    private $resourceConfig;
    
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->state = $state;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }
    
    public function getShopInfo() : array
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $shopInfo = [
            'plugin-version' => file_exists(__DIR__.'/../composer.json') ? json_decode(file_get_contents(__DIR__.'/../composer.json'))->version : null,
            'shop' => [
                'system' => 'Magento',
                'version' => 'Magento@'.\Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ProductMetadataInterface')->getVersion(),
            ],
            'environment' => array_merge(
                \Releva\Retargeting\Base\AbstractShopInfo::getServerEnvironment(),
                ['db' => \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection')->getConnection()->fetchRow('SELECT @@version AS `version`, @@version_comment AS `server`'),]
            ),
            'callbacks' => [
                'callback' => [
                    'url' => sprintf('%sreleva.nz/shopInfo', $baseUrl),
                    'parameters' => [],
                ],
                'export' => [
                    'url' => sprintf('%sreleva.nz/products', $baseUrl),
                    'parameters' => [
                        'format' => ['values' => ['csv', 'json', ], 'default' => 'csv', 'optional' => true, ],
                        'page' => ['type' => 'integer', 'default' => 0, 'optional' => true, 'info' => [
                            'items-per-page' => '@todo' // RelevanzProductExportController::$ITEMS_PER_PAGE,
                        ], ],
                    ],
                ],
            ]
        ];
        
        return $shopInfo;
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
    
    public function verifyApiKeyAndDisplayErrors (string $apiKey) :? \Releva\Retargeting\Base\Credentials
    {
        try {
            $credentials = \Releva\Retargeting\Base\RelevanzApi::verifyApiKey($apiKey, [
                'callback-url' => $this->getShopInfo()['callbacks']['callback']['url'],
            ]);
            $this->resourceConfig->saveConfig(
                self::XML_PATH_CLIENT_ID,
                $credentials->getUserId(),
                $this->getStoreId() ? \Magento\Store\Model\ScopeInterface::SCOPE_STORES : \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $this->getStoreId()
            );
            return $credentials;
        } catch (\Releva\Retargeting\Base\Exception\RelevanzException $exception) {
            $this->messageManager->addError(vsprintf($exception->getMessage(), $exception->getSprintfArgs()));
            return null;
        } catch (\Exception $exception) {
            $this->messageManager->addError(__($exception->getMessage()));
            return null;
        }
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