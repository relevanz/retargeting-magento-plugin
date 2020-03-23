<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 6:08 PM
 */
namespace Relevanz\Tracking\Block;

class AbstractTracking extends \Magento\Framework\View\Element\Template{

    const PROTOCOL      =   'https';
    const TRACKING_URL  =   'pix.hyj.mobi';
    const URL_PATH      =   'rt';

    protected $_clientId;
    protected $_scriptDefaultParams = array(
        'async' => 'true'
    );

    /**
     * @var \Relevanz\Tracking\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @param \Relevanz\Tracking\Helper\Data $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Relevanz\Tracking\Helper\Data $helper,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_clientId = (string) $this->_helper->getClientId();
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    protected function _isEnabled(){
        return false;
    }

    /**
     * @return array
     */
    protected function _getUrlParams(){
        return array();
    }

    /**
     * @param array $params
     * @return string
     */
    protected function _prepareScriptUrl($params = array()){
        $url = static::PROTOCOL . "://" . static::TRACKING_URL . '/' . static::URL_PATH;
        if(!empty($params)){
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * @param null $url
     * @param array $params
     * @return null|string
     */
    protected function _prepareScript($url = null, $params = array()){
        $script = null;
        if($url) {
            $params = array_merge($params, $this->_scriptDefaultParams);
            $script = '<script type="text/javascript" src="' . $url . '"';
            foreach ($params as $param => $value) {
                $script .= sprintf(' %s="%s"', $param, $value);
            }
            $script .= '>';
            $script .= '</script>';
        }
        return $script;
    }

    /**
     * @return bool
     */
    protected function _canTrack(){
        return (($this->_clientId
            && $this->_helper->isEnabled()
            && $this->_isEnabled()
        )
            ? true : false);
    }


    /**
     * @return string
     */
    protected function _getPreparedScript(){
        $params = $this->_getUrlParams();
        if(empty($params)){
            return array();
        }
        $params = array_merge($params, array('cid' => $this->_clientId));
        return $this->_prepareScript($this->_prepareScriptUrl($params), array());
    }


    /**
     * @return string
     */
    public function getScript(){
        return ($this->_canTrack()) ? $this->_getPreparedScript() : null;
    }
}
