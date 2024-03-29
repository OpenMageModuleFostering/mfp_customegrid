<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mfp_CustomeGrid_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		$collection->getSelect()->join(array(
    'item'=>$collection->getTable('sales/order_item')),
    'item.order_id=`main_table`.entity_id AND item.product_type="simple"',
    array(
        'sku' => new Zend_Db_Expr('group_concat(item.sku SEPARATOR ", ")'),
        'name' => new Zend_Db_Expr('group_concat(item.name SEPARATOR ", ")'),
    )
);

    $collection->getSelect()->group('main_table.entity_id');

//$collection->join('sales/order_item', 'order_id=entity_id', array('name'=>'name', 'sku' =>'sku', 'qty_ordered'=>'qty_ordered' ), null,'left');
		$collection->getSelect()->joinLeft('sales_flat_order', 'sales_flat_order.increment_id = main_table.increment_id',array('customer_firstname'));
$collection->getSelect()->joinLeft('sales_flat_order_address', 'sales_flat_order_address.parent_id = main_table.entity_id And address_type = "billing"',array('email','city','postcode','telephone'));
//$select->distinct();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
	 $sku = Mage::getStoreConfig('customegrid_section/customegrid_group/sku');
    $email = Mage::getStoreConfig('customegrid_section/customegrid_group/customer_email');
	$postcode = Mage::getStoreConfig('customegrid_section/customegrid_group/postcode');
	$city = Mage::getStoreConfig('customegrid_section/customegrid_group/city');
	$billing_name = Mage::getStoreConfig('customegrid_section/customegrid_group/billing_name');


        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
		'filter_index' => 'main_table.increment_id'

        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
						'filter_index' => 'main_table.store_id',

                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
									'filter_index' => 'main_table.created_at',

        ));
 if($sku == '1')
 {
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'index'     => 'sku',
			'type' => 'text',
			'filter_index' => 'item.sku'

        ));
		
		     $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'index'     => 'name',
			'type' => 'text',
			'filter_index' => 'item.name'

        ));

 }
  if($billing_name == '1')
 {
		         $this->addColumn('customer_firstname', array(
            'header'=> Mage::helper('sales')->__('First Name'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'customer_firstname',
			'filter_index' => 'sales_flat_order.customer_firstname'

        ));
		}
 if($email == '1')
 {

		
         $this->addColumn('email', array(
            'header'=> Mage::helper('sales')->__('Customer Email'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'email',
			'filter_index' => 'sales_flat_order_address.email'

        ));
 }

  if($city == '1')
 {

		         $this->addColumn('city', array(
            'header'=> Mage::helper('sales')->__('City'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'city',
			'filter_index' => 'sales_flat_order_address.city'

        ));
		
 }
 
   if($postcode == '1')
 {


				         $this->addColumn('postcode', array(
            'header'=> Mage::helper('sales')->__('Postcode'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'postcode',
						'filter_index' => 'sales_flat_order_address.postcode'

        ));
}

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
									'filter_index' => 'main_table.billing_name'

        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
												'filter_index' => 'main_table.shipping_name'

        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
															'filter_index' => 'main_table.base_grand_total'

        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
															'filter_index' => 'main_table.grand_total'

        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
	        'filter_index' => 'main_table.shipping_name'

        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
					'filter_index' => 'main_table.stores'
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
