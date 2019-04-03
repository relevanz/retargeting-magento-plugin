<?php

/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/28/17
 * Time: 10:04 AM
 */

namespace Relevanz\Tracking\Controller\Products;

use Magento\Framework\Controller\ResultFactory;
use Magento\CatalogInventory\Model\Stock;

class Index extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Relevanz\Tracking\Model\Products
     */
    private $productsModel;
    
    private $storeManager;
    
    private $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Relevanz\JsonExport\Model\Products $productsModel
     */
    public function __construct(
            \Relevanz\Tracking\Model\Products $productsModel,
            \Relevanz\Tracking\Helper\Data $helper,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\App\Action\Context $context
    ) {
        $this->productsModel = $productsModel;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        parent::__construct($context);
//        $this->galleryReadHandler   = $galleryReadHandler;
    }

    /**
     * @return int
     */
    protected function _getStoreId() {
        return (int) $this->storeManager->getStore()->getId();
    }

    /**
     * @param $product
     */
//    protected function _addGallery($product) {
//        $this->galleryReadHandler->execute($product);
//    }

    /**
     * @param $product
     * @return array
     */
//    protected function _getProductImages($product) {
//        $images = array();
//        if ($product instanceof \Magento\Catalog\Model\Product\Interceptor) {
////            $this->_addGallery($product);
//            $gallery = $product->getMediaGalleryImages();
//            if (count($gallery)) {
//                foreach ($gallery as $item) {
//                    $images[] = $item->getUrl();
//                }
//            }
//        }
//        return $images;
//    }
    
    private function getProducts($storeId) {
        $result = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $this->productsModel->getCollection($storeId);
        if ($collection) {
            foreach ($collection as $product) {
                $product->setStoreId($storeId);
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
                $result[] = array(
                    'product_id' => (int) $product->getId(),
                    'category_ids' => implode(",", $product->getCategoryIds()),
                    'product_name' => $product->getName(),
                    'short_description' => $product->getShortDescription(),
                    'price' => $product->getPrice(),
//                        'images' => $this->_getProductImages($product),
                    'stock_status' => ((((int) $product->getStockStatus()) == Stock::STOCK_IN_STOCK) ? 'IN_STOCK' : 'OUT_OF_STOCK')
                );
            }
        }
        return $result;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        if (!$this->helper->isAuthed($this->getRequest()->getParam('auth', ''))) {
            throw new \Exception('Not authed');
        }
        try {
            $storeId = $this->_getStoreId();
            $result = $this->getProducts($storeId);
        } catch (Exception $e) {
            $result = array(
                'status' => 'ERROR',
                'message' => $e->getMessage()
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $result = array(
                'status' => 'ERROR',
                'message' => $e->getMessage()
            );
        }
        $type = $this->getRequest()->getParam('type', 'json');
        $type = in_array($type, ['json', 'csv']) ? $type : 'json';
        if ($type === 'json') {
            $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $response->setData($result);
            return $response;
        } else/*if ($type === 'csv')*/ {
            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $stream = fopen('data://text/plain,' . "", 'w+');
            foreach ($result as $val) {
                fputcsv($stream, $val);
            }
            rewind($stream);
            $response->setContents( stream_get_contents($stream));
            return $response;
        }
    }

}
