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
use Releva\Retargeting\Base\Export\ExporterInterface;
use Releva\Retargeting\Base\Export\ProductJsonExporter;
use Releva\Retargeting\Base\Export\ProductCsvExporter;
use Releva\Retargeting\Base\Export\Item\ProductExportItem;
use Magento\Store\Api\Data\StoreInterface;

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
    
    protected function getStore() : StoreInterface
    {
        return $this->storeManager->getStore();
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

    private function getProducts(string $type, StoreInterface $store) :? ExporterInterface
    {
        $objectManager = ObjectManager::getInstance();
        $page = $this->getRequest()->getParam('page');
        $collection = $this->productsModel->getCollection($store, $page === null ? null : (((int) $page) + 1));
        if ($collection === null) {
            return null;
        } else {
            $exporter = $type === 'json' ? new ProductJsonExporter() : new ProductCsvExporter();
            foreach ($collection as $product) {
                $product->setStore($store);
                $product = $objectManager->create(Product::class)->load($product->getId());
                $exporter->addItem(new ProductExportItem(
                        (int) $product->getId(),
                        $product->getCategoryIds(),
                        $product->getName(),
                        $product->getShortDescription(),
                        $product->getDescription(),
                        $product->getPrice(),
                        $product->getPrice(),//@todo priceOffer
                        $product->getProductUrl(),
                        $this->getProductImage($product)
                ));
            }
            return $exporter;
        }
    }

    public function execute() : ResultInterface
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        if (!$this->helper->isAuthed($this->getRequest()->getParam('auth', ''))) {
            $response->setHttpResponseCode(401);
        } else {
            try {
                $type = $this->getRequest()->getParam('type', 'csv');
                $exporter = $this->getProducts($type === 'json' ? $type : 'csv', $this->getStore());
                if ($exporter === null) {
                    $response->setHttpResponseCode(404);
                } else {
                    $response->setContents($exporter->getContents());
                    foreach ($exporter->getHttpHeaders() as $key => $value) {
                        $response->setHeader($key, $value);
                    }
                }
            } catch (\Exception $e) {
                $response->setContents($e->getMessage());
                $response->setHttpResponseCode(500);
            }
        }
        return $response;
    }
    
}
