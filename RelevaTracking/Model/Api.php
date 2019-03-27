<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/26/17
 * Time: 1:13 PM
 */

namespace Extensions\RelevaTracking\Model;

class Api{

    const PROTOCOL          =   'https';
    const STATISTIC_URL     =   'api.hyj.mobi';

    protected $_verificationTimeOut  = 60;
    protected $_localeDate;
    protected $curlFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        array $data = [])
    {
        $this->_localeDate  =   $context->getLocaleDate();
        $this->curlFactory  =   $curlFactory;
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    protected function _prepareUrl($path = null, $params = array()){
        $url = static::PROTOCOL . "://" . static::STATISTIC_URL;
        if($path){
            $url .= '/' . $path;
        }
        if(!empty($params)){
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * @param string $date
     * @return string
     */
    protected function _formatDate($date = null){
        $scopeDate = $this->_localeDate->scopeDate(null, $date, false);
        return date("Y-m-d", $scopeDate->getTimestamp());
    }

    /**
     * @param string $apiKey
     * @return bool|\Magento\Framework\DataObject
     */
    public function getUser($apiKey = null){
        $url = $this->_prepareUrl('user/get', array('apikey' => $apiKey));
        return $this->_request($url);
    }

    /**
     * @param string $apiKey
     * @param string $from
     * @param string $to
     * @return bool|\Magento\Framework\DataObject
     */
    public function getStatisticData($apiKey = null, $from = null, $to = null){
        $from = ($from) ? $this->_formatDate($from) : null;
        $to = ($to) ? $this->_formatDate($to) : null;
        $url = $this->_prepareUrl('stats', array('apikey' => $apiKey, 'startdate' => $from, 'enddate' => $to));
        return $this->_request($url);
    }

    /**
     * @param string $url
     * @return bool|\Magento\Framework\DataObject
     */
    protected function _request($url = null){
        $response = new \Magento\Framework\DataObject();
        try {
            $curl = $this->curlFactory->create();
            $curl->setConfig(
                [
                    'timeout'   => $this->_verificationTimeOut
                ]
            );
            $curl->write(\Zend_Http_Client::GET, $url, '1.0');
            $data = $curl->read();
            if ($data === false) {
                return false;
            }
            $responseCode = \Zend_Http_Response::extractCode($data);
            $data = preg_split('/^\r?$/m', $data, 2);
            $data = trim($data[1]);

            if($responseCode == 200) {
                $response->setData(array(
                    'status' => 'success',
                    'result' => $data
                ));
            }
            else{
                $data = json_decode($data);
                $response->setData(array(
                    'status' => 'error',
                    'message' => (isset($data->message) && $data->message) ? $data->message : ((isset($data->code) && $data->code) ? $data->code : 'Error!')
                ));
            }
            $curl->close();
        } catch (Exception $e) {
            $response->setData(
                array(
                    'status' => 'error',
                    'message' => $e->getMessage())
            );
        }
        return $response;
    }
}