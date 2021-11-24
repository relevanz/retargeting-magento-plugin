<?php declare(strict_types = 1);

/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/28/17
 * Time: 10:04 AM
 */

namespace Relevanz\Tracking\Controller\Products;

use Magento\Framework\App\Action\Action;
use Relevanz\Tracking\Model\Products;
use Magento\Framework\Controller\ResultFactory;
use Relevanz\Tracking\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action
{
    
    private $productsModel;
    
    private $storeManager;
    
    private $helper;
    
    public function __construct(Products $productsModel, Data $helper, StoreManagerInterface $storeManager, Context $context)
    {
        $this->productsModel = $productsModel;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return int
     */
    protected function _getStoreId() : int
    {
        return (int) $this->storeManager->getStore()->getId();
    }
    
    private function getProductCategoryId (Product $product) : string
    {
        $categoryIds = $product->getCategoryIds();
        $categoryId = count($categoryIds) ? current($categoryIds) : '';
        return $categoryId;
    }
    
    private function getProductImage (Product $product) :? string
    {
        $baseImage = $product->getImage();
        $image = null;
        foreach ($product->getMediaGalleryImages() as $mediaImage) {
            if ($mediaImage->getMediaType() === 'image' && !$mediaImage->getDisabled()) {
                if ($image === null) {// use first image
                    $image = $mediaImage->getUrl();
                }
                if ($mediaImage->getFile() === $baseImage) {// if image eq. to baseimage use current image
                    $image = $mediaImage->getUrl();
                    break;
                }
            }
        }
        return $image;
    }

    private function getProducts($storeId) :? array
    {
        $objectManager = ObjectManager::getInstance();
        $page = $this->getRequest()->getParam('page');
        $collection = $this->productsModel->getCollection($storeId, $page === null ? null : (((int) $page) + 1));
        if ($collection === null) {
            return null;
        } else {
            $result = [];
            foreach ($collection as $product) {
                $product->setStoreId($storeId);
                $product = $objectManager->create(Product::class)->load($product->getId());
                $result[] = array(
                    'product_id' => (int) $product->getId(),
                    'category_ids' => $this->getProductCategoryId($product),
                    'product_name' => $product->getName(),
                    'short_description' => $product->getShortDescription(),
                    'long_description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'link' => $product->getProductUrl(),
                    'image' => $this->getProductImage($product),
                );
            }
            return $result;
        }
    }

    public function execute() : ResultInterface
    {
        if (!$this->helper->isAuthed($this->getRequest()->getParam('auth', ''))) {
            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $response->setHttpResponseCode(401);
            return $response;
        }
        try {
            $storeId = $this->_getStoreId();
            $result = $this->getProducts($storeId);
        } catch (\Exception $e) {
            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $response->setContents($e->getMessage());
            $response->setHttpResponseCode(500);
            return $response;
        }
        if ($result === null) {
            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $response->setHttpResponseCode(404);
            return $response;
        }
        $type = $this->getRequest()->getParam('type', 'json');
        $type = in_array($type, ['json', 'csv']) ? $type : 'json';
        if ($type === 'json') {
            $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $response->setData($result);
            return $response;
        } else/*if ($type === 'csv')*/ {
            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $stream = fopen('data://text/plain,', 'w+');
            foreach ($result as $val) {
                fputcsv($stream, $val, ',', '"');
            }
            rewind($stream);
            $response->setContents(stream_get_contents($stream));
            return $response;
        }
    }
    
}
