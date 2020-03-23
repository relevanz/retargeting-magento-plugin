<?php
/**
 * Created by:
 * User: Oleg G
 * Email: oleg.galch87@gmail.com
 * Date: 6/22/17
 * Time: 5:41 PM
 */

namespace Relevanz\Tracking\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Statistics controller
 */
abstract class Statistics extends Action
{
    
    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Relevanz_Tracking::statistics')->_addBreadcrumb(__('releva.nz Dashboard'), __('releva.nz Dashboard'));
        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Relevanz_Tracking::statistics');
    }
}