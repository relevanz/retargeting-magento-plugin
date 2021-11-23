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
        return $this->_helper->getRegistry()->registry('current_product');
    }

    protected function getScriptUrl(string $clientId) {
        return \Releva\Retargeting\Base\RelevanzApi::RELEVANZ_TRACKER_URL.'?'.http_build_query(array_merge(
            [
                'cid' => $clientId,
                't' => 'd',
                'action' => 'p',
            ],
            $this->_getProduct() === null ? [] : ['id' => $this->_getProduct()->getId()]
        ));
    }
}