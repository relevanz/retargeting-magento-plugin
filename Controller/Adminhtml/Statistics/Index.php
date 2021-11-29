<?php declare(strict_types = 1);
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 5:43 PM
 */

namespace Relevanz\Tracking\Controller\Adminhtml\Statistics;

use Relevanz\Tracking\Controller\Adminhtml\Statistics;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as ResultPage;

class Index extends Statistics
{
    
    /**
     * @var PageFactory
     */
    private $pageFactory;
    
    public function __construct(PageFactory $pageFactory, Context $context)
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }
    
    public function execute() : ResultPage
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Relevanz_Tracking::tastefinder');
        $resultPage->getConfig()->getTitle()->prepend(__('releva.nz Dashboard'));
        $resultPage->addBreadcrumb(__('relevan.nz'), __('relevan.nz'));
        $resultPage->addBreadcrumb(__('relevan.nz Dashboard'), __('releva.nz Dashboard'));
        return $resultPage;
    }
    
}