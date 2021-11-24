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
use Magento\Catalog\Model\Category as MagentoCategory;
use Releva\Retargeting\Base\RelevanzApi;

class Category extends AbstractTracking
{
    
    protected function getCurrentCategory() :? MagentoCategory
    {
        return $this->helper->getRegistry()->registry('current_category');
    }
    
    protected function getScriptUrl(string $clientId) : string
    {
        return RelevanzApi::RELEVANZ_TRACKER_URL.'?'.http_build_query(array_merge(
            [
                'cid' => $clientId,
                't' => 'd',
                'action' => 'c',
            ],
            $this->getCurrentCategory() === null ? [] : ['id' => $this->getCurrentCategory()->getId()]
        ));
    }
    
}