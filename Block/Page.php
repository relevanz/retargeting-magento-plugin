<?php declare(strict_types = 1);
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 6:01 PM
 */

/**
 * tracking url example: https://pix.hyj.mobi/rt?t=d&action=s&cid=CLIENT_ID
 */

namespace Relevanz\Tracking\Block;

use Relevanz\Tracking\Block\AbstractTracking;
use Releva\Retargeting\Base\RelevanzApi;

class Page extends AbstractTracking{
    
    protected function getScriptUrl(string $clientId) : string
    {
        return RelevanzApi::RELEVANZ_TRACKER_URL.'?'.http_build_query([
            'cid' => $clientId,
            't' => 'd',
            'action' => 's'
        ]);
    }
    
}