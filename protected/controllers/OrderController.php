<?php

class OrderController extends Controller{

	/**
	 * @return array action filters
	 */
	public function filters(){
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules(){
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*')
			)
		);
	}

	public function actionSales($isShipped='false', $page=0, $pageSize=30, 
								$customerId=0, $EstateSTR=0, $productTypeId=0, 
								$startDate='', $endDate='',
								$sortField='OrderDate', $sortDir='DESC',
								$keywords=''){
		$isShipped = $isShipped === 'false' ? false : true ;
		$page = $page === '' ? 0 : $page;
		$orderModel = new Order();
		$customers = $orderModel->getCustomerList($isShipped);
		$customerOrderTypes = $orderModel->getCustomerOrderTypes($isShipped);
		$salesOrders = $orderModel->getSalesRecordByCustomer($customerId, $isShipped, $page, $pageSize, 
															 $EstateSTR, $productTypeId, 
															 $startDate, $endDate,
															 $sortField, $sortDir, 
															 $keywords);
		$getPriceQtyRMBAmountSum = $orderModel->getPriceQtyRMBAmountSum($customerId, $isShipped, $page, $pageSize, 
															 $EstateSTR, $productTypeId, 
															 $startDate, $endDate,
															 $keywords);
		
		//Pagination
		$count = $orderModel->getSalesRecordByCustomerCount($customerId, $isShipped, $EstateSTR, $productTypeId, 
															$startDate, $endDate, $keywords);
		$pageNum = ceil( $count / $pageSize );
		
		//Columns for render:
		$columns = array(
			'Forshort'=>array('label'=>'客戶'),
			'CkName'=>array('label'=>'倉庫'),
			'cName'=>array('label'=>'品名', 'class'=>'col-md-4'),
			'Price'=>array('label'=>'單價', 'class'=>'text-right'),
			'Qty'=>array('label'=>'數量', 'class'=>'text-right'),
			'Unit'=>array('label'=>'單位'),
			'RMBamount'=>array('label'=>'銷售額(RMB)', 'class'=>'text-right'),
			'OrderDate'=>array('label'=>'下單日期')
		);

		$this->render('sales', compact('customers', 
									   'customerOrderTypes', 
									   'salesOrders', 
									   'columns', 
									   'count', 
									   'isShipped',
									   'page',
									   'pageSize',
									   'customerId',
									   'EstateSTR',
									   'productTypeId',
									   'startDate',
									   'endDate',
									   'sortField',
									   'sortDir',
									   'keywords',
									   'pageNum',
									   'getPriceQtyRMBAmountSum'));
	}
}