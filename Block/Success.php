<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/16/17
 * Time: 6:05 PM
 */

/**
 * tracking url example: https://d.hyj.mobi/convNetw?cid=CLIENT_ID&orderId=ORDER_ID&amount=ORDER_TOTAL&eventName=ARTILE_ID1,ARTILE_ID2,ARTILE_ID3&network=relevanz
 */

namespace Relevanz\Tracking\Block;

class Success extends \Relevanz\Tracking\Block\AbstractTracking{


    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Relevanz\Tracking\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Relevanz\Tracking\Helper\Data $helper,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        parent::__construct($helper, $context, $data);
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder(){
        return $this->_checkoutSession->getLastRealOrder();
    }
    
    protected function getScriptUrl(string $clientId) {
        $order  = $this->getOrder();
        $params = [];
        if($order instanceof \Magento\Sales\Model\Order) {
            $itemsIds = [];
            foreach ($order->getAllVisibleItems() as $product) {
                $itemsIds[] = $product->getProductId();
            }
            $params = array(
                'orderId' => (string)$order->getIncrementId(),
                'amount' => number_format($order->getGrandTotal(), 2, '.', ''),
                'eventName' => implode(',', $itemsIds),
            );
        }
        return \Releva\Retargeting\Base\RelevanzApi::RELEVANZ_CONV_URL.'?'.http_build_query(array_merge(
            [
                'cid' => $clientId,
                'network' => 'relevanz',
            ],
            $params
        ));
    }
}