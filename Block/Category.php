<?php declare(strict_types = 1);
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

use Relevanz\Tracking\Block\AbstractTracking;

class Category extends AbstractTracking
{
    
    protected function getParameters() : array
    {
        $parameters = [
            't' => 'd',
            'action' => 'c',
        ];
        if ($category = $this->helper->getRegistry()->registry('current_category')) {
            /* @var $category \Magento\Catalog\Model\Category */
            $parameters['id'] = $category->getId();
        }
        return $parameters;
    }
    
}