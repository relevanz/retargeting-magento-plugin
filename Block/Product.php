<?php declare(strict_types = 1);
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

use Relevanz\Tracking\Block\AbstractTracking;
use Releva\Retargeting\Base\RelevanzApi;
use Magento\Catalog\Model\Product as MagentoProduct;

class Product extends AbstractTracking
{
    
    protected function getCurrentProduct() :? MagentoProduct
    {
        return $this->helper->getRegistry()->registry('current_product');
    }
    
    protected function getScriptUrl(string $clientId) : string
    {
        return RelevanzApi::RELEVANZ_TRACKER_URL.'?'.http_build_query(array_merge(
            [
                'cid' => $clientId,
                't' => 'd',
                'action' => 'p',
            ],
            $this->getCurrentProduct() === null ? [] : ['id' => $this->getCurrentProduct()->getId()]
        ));
    }
    
}