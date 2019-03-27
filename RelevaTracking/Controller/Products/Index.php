<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/28/17
 * Time: 10:04 AM
 */

namespace Extensions\RelevaTracking\Controller\Products;

use Magento\Framework\Controller\ResultFactory;
use Magento\CatalogInventory\Model\Stock;

class Index extends \Magento\Framework\App\Action\Action{

    /**
     * @var \Extensions\RelevaTracking\Model\Products
     */
    protected $_productsModel;


  
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Extensions\JsonExport\Model\Products $productsModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Extensions\RelevaTracking\Model\Products $productsModel
    )
    {
        parent::__construct($context);
        $this->_productsModel       = $productsModel;
//        $this->galleryReadHandler   = $galleryReadHandler;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store', 0);
    }

    /**
     * @param $product
     */
    protected function _addGallery($product)
    {
        $this->galleryReadHandler->execute($product);
    }

    /**
     * @param $product
     * @return array
     */
    protected function _getProductImages($product){
        $images = array();
        if($product instanceof \Magento\Catalog\Model\Product\Interceptor){
//            $this->_addGallery($product);
            $gallery = $product->getMediaGalleryImages();
            if(count($gallery)){
                foreach ($gallery as $item) {
                    $images[] = $item->getUrl();
                }
            }
        }
        return $images;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result       =   array();
        try {
            $storeId = $this->_getStoreId();
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $collection = $this->_productsModel->getCollection($storeId);
            if ($collection) {
                foreach ($collection as $product) {
                    $product->setStoreId($storeId);
				    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());        
                    $result[] = array(
                        'product_id'        =>  (int)$product->getId(),
                        'category_ids'      =>  implode(",", $product->getCategoryIds()),
                        'product_name'      =>  $product->getName(),
                        'short_description' =>  $product->getShortDescription(),
                        'price'             =>  $product->getPrice(),
                        'images'            =>  $this->_getProductImages($product),
                        'stock_status'      =>  (($product->getStockStatus() == Stock::STOCK_IN_STOCK) ? 'IN_STOCK' : 'OUT_OF_STOCK')
                    );
                }
            }
        }
        catch(Exception $e){
            $result = array(
                'status'    =>  'ERROR',
                'message'   =>  $e->getMessage()
            );
        }
        catch(\Magento\Framework\Exception\NoSuchEntityException $e){
            $result = array(
                'status'    =>  'ERROR',
                'message'   =>  $e->getMessage()
            );
        }
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData($result);
        return $response;
    }

}