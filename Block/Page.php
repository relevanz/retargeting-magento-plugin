<?php
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

class Page extends \Relevanz\Tracking\Block\AbstractTracking{

    protected function getScriptUrl(string $clientId) {
        return \Releva\Retargeting\Base\RelevanzApi::RELEVANZ_TRACKER_URL.'?'.http_build_query([
            'cid' => $clientId,
            't' => 'd',
            'action' => 's'
        ]);
    }
}