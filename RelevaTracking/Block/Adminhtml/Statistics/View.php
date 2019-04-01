<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 6:11 PM
 */

namespace Relevanz\Tracking\Block\Adminhtml\Statistics;

class View extends \Magento\Backend\Block\Widget{

    protected $_backendHelper;
    protected $_backendSession;
    protected $_timezoneInterface;
    protected $_datetime;
    protected $_adminHelper;
    protected $_api;

    protected $_template = 'Relevanz_Tracking::statistics/view.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Relevanz\Tracking\Helper\Admin\Data $adminHelper
     * @param \Relevanz\Tracking\Model\Api $api
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Relevanz\Tracking\Helper\Admin\Data $adminHelper,
        \Relevanz\Tracking\Model\Api $api,
        array $data = []
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_adminHelper = $adminHelper;
        $this->_api     =   $api;
        parent::__construct($context, $data);
    }
    
    public function isStore() {
        return $this->_getStoreId() !== 0;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store', 0);
    }
    
    public function getApiKey($storeId = 0){
        return $this->_adminHelper->getApiKey($this->_getStoreId());
    }

    /**
     * @return bool
     */
    public function validateApiKey()
    {
        $apiKey = $this->_adminHelper->getApiKey($this->_getStoreId());
        if(!$apiKey){
            return false;
        }
        $validateApiKey = $this->_api->getUser($apiKey);
        if($validateApiKey->getStatus() != 'success'){
            return false;
        }
        return true;
    }

    /**
     * Get date format according the locale
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_localeDate->getDateFormatWithLongYear();
    }

    /**
     * @return string
     */
    public function getReportTo()
    {
        $date = new \DateTime();
        $value = $this->_localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE
        );
        return $value;
    }

    /**
     * @return string
     */
    public function getReportFrom()
    {
        $date   = new \DateTime();
        $date->modify('-1 month');
        $value  = $this->_localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE
        );
        return $value;
    }

    /**
     * @return string
     */
    public function getDataUrl(){
        $params = array();
        if($storeId = $this->_getStoreId()) {
            $params['store'] = $storeId;
        }
        return $this->getUrl('*/*/data', $params);
    }

    /**
     * Api Key Post Url
     * @return string
     */
    public function apiKeyPostUrl(){
        $params = array();
        if($storeId = $this->_getStoreId()) {
            $params['store'] = $storeId;
        }
        return $this->getUrl('*/*/keyPost', $params);
    }

    /**
     * @return string
     */
    public function getKeyValidationUrl(){
        $params = array();
        if($storeId = $this->_getStoreId()) {
            $params['store'] = $storeId;
        }
        return $this->getUrl('*/*/keyValidation', $params);
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareFilterButtons();
        return parent::_beforeToHtml();
    }

    /**
     * Return refresh button html
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getRefreshButtonHtml()
    {
        return $this->getChildHtml('refresh_button');
    }

    /**
     * Return submit api key button html
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getSubmitButtonHtml()
    {
        return $this->getChildHtml('submit_key_button');
    }

    /**
     * Retrieve grid javascript object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return preg_replace("~[^a-z0-9_]*~i", '', $this->getId()) . 'JsObject';
    }

    /**
     * Prepare buttons
     *
     * @return void
     */
    protected function _prepareFilterButtons()
    {
        $this->addChild(
            'refresh_button',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Refresh'), 'class' => 'task', 'id' => $this->getSuffixId('refresh_statistics')]
        );

        $this->addChild(
            'submit_key_button',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Submit'), 'class' => 'task', 'id' => $this->getSuffixId('submit_key_button')]
        );
    }

}