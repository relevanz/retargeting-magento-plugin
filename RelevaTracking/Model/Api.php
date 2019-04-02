<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/26/17
 * Time: 1:13 PM
 */

namespace Relevanz\Tracking\Model;

class Api{

    const PROTOCOL          =   'https';
    const STATISTIC_URL     =   'api.hyj.mobi';

    protected $_verificationTimeOut  = 60;
    protected $_localeDate;
    protected $curlFactory;
    
    private $resourceConfig;
    
    private $request;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        array $data = [])
    {
        $this->resourceConfig = $resourceConfig;
        $this->request = $request;
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
     * @param string $apiKey
     * @return bool|\Magento\Framework\DataObject
     */
    public function getUser($apiKey = null){
        $url = $this->_prepareUrl('user/get', array('apikey' => $apiKey));
        $response = $this->_request($url);
        if ($response->getStatus() === 'success') {
            $result = json_decode($response->getResult());
            if(isset($result->user_id) && ($clientId = $result->user_id)) {
                $storeId = $this->request->getParam('store', 0);
                $scope = ($storeId) ? \Magento\Store\Model\ScopeInterface::SCOPE_STORES : \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
                $this->resourceConfig->saveConfig(
                    \Relevanz\Tracking\Helper\Admin\Data::XML_PATH_CLIENT_ID,
                    $clientId,
                    $scope,
                    $storeId
                );
            }
        } else {
            throw new \Exception($response->getMessage());
        }
        return $response;
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
        } catch (\Exception $e) {
            $response->setData(
                array(
                    'status' => 'error',
                    'message' => $e->getMessage())
            );
        }
        return $response;
    }
}