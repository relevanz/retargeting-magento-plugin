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
        return $this->_registry->registry('current_category');
    }

    /**
     * @return string
     */
    protected function _isEnabled(){
        return $this->_helper->isCategoryTrackEnabled();
    }

    /**
     * @return array
     */
    protected function _getUrlParams(){
        $params   = array();
        $category = $this->_getCategory();
        if($category instanceof \Magento\Catalog\Model\Category) {
            $params = array(
                't'         => 'd',
                'action'    => 'c',
                'id'        => $category->getId()
            );
        }
        return $params;
    }
}