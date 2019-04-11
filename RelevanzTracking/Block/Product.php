<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 6:05 PM
 */

/**
 * tracking url example: https://pix.hyj.mobi/rt?t=d&action=p&cid=CLIENT_ID&id=PRODUCT_ID
 */

namespace Relevanz\Tracking\Block;

class Product extends \Relevanz\Tracking\Block\AbstractTracking{


    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function _getProduct(){
        return $this->_registry->registry('current_product');
    }

    /**
     * @return string
     */
    protected function _isEnabled(){
        return $this->_helper->isProductTrackEnabled();
    }

    /**
     * @return array
     */
    protected function _getUrlParams(){
        $params  = array();
        $product = $this->_getProduct();
        if($product instanceof \Magento\Catalog\Model\Product) {
            $params = array(
                't'         => 'd',
                'action'    => 'p',
                'id'        => $product->getId()
            );
        }
        return $params;
    }
}