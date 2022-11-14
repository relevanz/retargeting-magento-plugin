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

class Product extends AbstractTracking
{
    
    protected function getParameters(): array {
        $parameters = [
            't' => 'd',
            'action' => 'p',
        ];
        if ($product = $this->helper->getRegistry()->registry('current_product')) {
            /* @var $product \Magento\Catalog\Model\Product */
            $parameters['id'] = $product->getId();
        }
        return $parameters;
    }
    
}