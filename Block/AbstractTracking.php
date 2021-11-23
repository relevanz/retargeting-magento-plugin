<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 6:08 PM
 */
namespace Relevanz\Tracking\Block;

abstract class AbstractTracking extends \Magento\Framework\View\Element\Template{

    /**
     * @var \Relevanz\Tracking\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Relevanz\Tracking\Helper\Data $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Relevanz\Tracking\Helper\Data $helper,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    
    public function isActive() {
        return $this->_helper->getClientId() && $this->_helper->isEnabled();
    }
    
    public function getAdditionalHtml()
    {
        return $this->_helper->getAdditionalHtml();
    }
    
    abstract protected function getScriptUrl(string $clientId);
    
    public function getScriptParameters () {
        return [
            'src' => $this->getScriptUrl((string) $this->_helper->getClientId()),
            'async' => 'true',
        ];
    }
}
