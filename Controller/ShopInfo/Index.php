<?php declare(strict_types = 1);
namespace Relevanz\Tracking\Controller\Shopinfo;

use Relevanz\Tracking\Helper\Data as DataHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    
    private $helper;

    public function __construct(DataHelper $helper, Context $context)
    {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute() : ResultInterface
    {
        if (!$this->helper->isAuthed($this->getRequest()->getParam('auth', ''))) {
            throw new \Exception('Not authed');
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($this->helper->getShopInfo());
    }

}
