<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 6:05 PM
 */

/**
 * tracking url example: https://pix.hyj.mobi/rt?t=d&action=c&cid=CLIENT_ID&id=CATEGORY_ID
 */

namespace Relevanz\Tracking\Block;

class Category extends \Relevanz\Tracking\Block\AbstractTracking{

    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function _getCategory(){
        return $this->_helper->getRegistry()->registry('current_category');
    }
    
    protected function getScriptUrl(string $clientId) {
        return \Releva\Retargeting\Base\RelevanzApi::RELEVANZ_TRACKER_URL.'?'.http_build_query(array_merge(
            [
                'cid' => $clientId,
                't' => 'd',
                'action' => 'c',
            ],
            $this->_getCategory() === null ? [] : ['id' => $this->_getCategory()->getId()]
         ));
    }
}