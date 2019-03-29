<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/28/17
 * Time: 10:41 AM
 */

namespace Relevanz\Tracking\Model;

use Magento\Store\Model\Store;

class Products{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;


    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager
    ){
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param int $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function _getStore($storeId = 0)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @param int $storeId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollection($storeId = 0){
        $store = $this->_getStore($storeId);
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'type_id'
        )->setStore(
            $store
        );
        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
            $collection->joinField(
                'stock_status',
                'cataloginventory_stock_status',
                'stock_status',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
//                Store::DEFAULT_STORE_ID
                $store->getId()
            );
//            $collection->joinAttribute(
//                'custom_name',
//                'catalog_product/name',
//                'entity_id',
//                null,
//                'inner',
//                $store->getId()
//            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            $collection->joinAttribute('short_description', 'catalog_product/short_description', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('short_description');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        return $collection;
    }

}