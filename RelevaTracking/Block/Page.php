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

namespace Extensions\RelevaTracking\Block;

class Page extends \Extensions\RelevaTracking\Block\AbstractTracking{

    /**
     * @return string
     */
    protected function _isEnabled(){
        return $this->_helper->isFrontPageTrackEnabled();
    }

    /**
     * @return array
     */
    protected function _getUrlParams(){
        return array(
            't'         => 'd',
            'action'    => 's'
        );
    }
}