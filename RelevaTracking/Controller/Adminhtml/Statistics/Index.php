<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 5:43 PM
 */

namespace Extensions\RelevaTracking\Controller\Adminhtml\Statistics;

class Index extends \Extensions\RelevaTracking\Controller\Adminhtml\Statistics
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
        $resultPage->setActiveMenu('Extensions_RelevaTracking::tastefinder');
        $resultPage->getConfig()->getTitle()->prepend(__('releva.nz Dashboard'));
        $resultPage->addBreadcrumb(__('Extensions'), __('Extensions'));
        $resultPage->addBreadcrumb(__('Releva Dashboard'), __('releva.nz Dashboard'));
        return $resultPage;
    }
}