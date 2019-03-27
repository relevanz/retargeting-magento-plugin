<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/26/17
 * Time: 5:56 PM
 */
namespace Extensions\RelevaTracking\Helper\Admin;

class Data extends \Magento\Framework\App\Helper\AbstractHelper{

    const XML_PATH_CLIENT_ID    = 'extensions_relevatracking/settings/client_id';
    const XML_PATH_API_KEY      = 'extensions_relevatracking/settings/api_key';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->_scopeConfig     = $this->scopeConfig;
        $this->_resource        = $resource;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getApiKey($storeId = 0){

        $scope      = ($storeId) ? \Magento\Store\Model\ScopeInterface::SCOPE_STORES : \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $connection = $this->_resource->getConnection();
        $tableName  = $connection->getTableName('core_config_data');

        $select = $connection->select()->from(
            $tableName,
            ['api_key' => 'value']
        )->where(
            'path = ?',
            self::XML_PATH_API_KEY
        )->where(
            'scope = ?',
            $scope
        )->where(
            'scope_id = ?',
            $storeId
        );
        $row = $connection->fetchRow($select);
        return ($row && isset($row['api_key'])) ? $row['api_key'] : '';

//        return $this->_scopeConfig->getValue(
//            self::XML_PATH_API_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
//            $this->_storeManager->getStore($storeId));
    }

}