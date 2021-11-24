<?php declare(strict_types = 1);
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

use Relevanz\Tracking\Block\AbstractTracking;
use Magento\Sales\Model\Order as MagentoOrder;
use Releva\Retargeting\Base\RelevanzApi;

class Success extends AbstractTracking
{
    
    protected function getOrder() :? MagentoOrder
    {
        return $this->helper->getCheckoutSession()->getLastRealOrder();
    }
    
    protected function getScriptUrl(string $clientId) : string
    {
        $order = $this->getOrder();
        $params = [];
        if($order !== null) {
            $itemsIds = [];
            foreach ($order->getAllVisibleItems() as $product) {
                $itemsIds[] = $product->getProductId();
            }
            $params = array(
                'orderId' => (string) $order->getIncrementId(),
                'amount' => number_format((float) $order->getGrandTotal(), 2, '.', ''),
                'eventName' => implode(',', $itemsIds),
            );
        }
        return RelevanzApi::RELEVANZ_CONV_URL.'?'.http_build_query(array_merge(
            [
                'cid' => $clientId,
                'network' => 'relevanz',
            ],
            $params
        ));
    }
}