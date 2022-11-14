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

class Page extends AbstractTracking{
    
    protected function getParameters() : array
    {
        return [
            't' => 'd',
            'action' => 's',
        ];
    }
    
}