<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 5:43 PM
 */

namespace Relevanz\Tracking\Controller\Adminhtml\Statistics;

class Index extends \Relevanz\Tracking\Controller\Adminhtml\Statistics
{
    /**
     * Statistics list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Relevanz_Tracking::tastefinder');
        $resultPage->getConfig()->getTitle()->prepend(__('releva.nz Dashboard'));
        $resultPage->addBreadcrumb(__('relevan.nz'), __('relevan.nz'));
        $resultPage->addBreadcrumb(__('relevan.nz Dashboard'), __('releva.nz Dashboard'));
        return $resultPage;
    }
}