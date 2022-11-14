<?php declare(strict_types = 1);
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/28/17
 * Time: 10:41 AM
 */

namespace Relevanz\Tracking\Model;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Module\Manager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class Products
{

    static $pageLimit = 100;

    protected $productFactory;

    protected $moduleManager;

    public function __construct(ProductFactory $productFactory, Manager $moduleManager)
    {
        $this->productFactory = $productFactory;
        $this->moduleManager = $moduleManager;
    }

    public function getCollection(StoreInterface $store = null, int $page = null, int $limit) :? ProductCollection
    {
        $collection = $this->productFactory->create()->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->setStore($store)
        ;
        $collection->getSelect()->where('e.type_id = "simple"');
        if ($page !== null) {
            $collection->setPage($page, $limit === 0 ? self::$pageLimit : $limit);
        }
        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection
                ->joinField('qty', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left')
                ->joinField('stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left')
            ;
        }
        if ($store !== null && $store->getId()) {
            $collection
                ->addStoreFilter($store)
                ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId())
                ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId())
                ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId())
                ->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId())
                ->joinAttribute('short_description', 'catalog_product/short_description', 'entity_id', null, 'left', $store->getId())
            ;
        } else {
            $collection
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('short_description')
                ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner')
                ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner')
            ;
        }
        return $page !== null && $collection->getLastPageNumber() < $page ? null : $collection;
    }

}