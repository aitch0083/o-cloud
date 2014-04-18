<?php

class Order extends CActiveRecord{

	public static $db = null;
	private static $targetDB = 'NONE';

	/**
	 * @return resource target database conection
	*/
	protected static function getDbInConnection(){
		if(self::$db !== null){
			return self::$db;
		}else{
			self::$db = Yii::app()->dbIn;
			if(self::$db instanceof CDbConnection){
				self::$db->setActive(true);
				return self::$db;
			}else{
				throw new CDbException(Yii::t('yii', 'Unable to connect to DB:[{db}]', array('db', self::$targetDB)));
			}
		}
	}

	public function getDbConnection(){
        return self::getDbInConnection();
    }

	/**
	 * @return string associated table name
	 */
	public function tableName(){
		return 'clientdata';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array();
	}

	public function getCustomerList($isShipped=false){
		
		$whereClouse = ($isShipped === false) ? 'B.Estate>0  AND B.Estate!=4' : 'B.Estate=0' ;
		$command = self::$db->createCommand('SELECT A.CompanyId, C.Forshort, SUM(B.Qty*B.Price*D.Rate) AS Amount,D.Rate,D.Symbol 
											  FROM data_in.yw1_salesmain A 
											  LEFT JOIN data_in.yw1_salessheet B ON A.OrderNumber=B.OrderNumber 
											  LEFT JOIN data_in.clientdata C ON A.CompanyId=C.CompanyId 
											  LEFT JOIN data_public.currencydata D ON D.Id=C.Currency 
											  WHERE '.$whereClouse.' GROUP BY A.CompanyId 
											  ORDER BY Amount DESC, A.CompanyId ASC');
		
		return $command->queryAll();
	}

	public function getCustomerOrderTypes($isShipped=false){

		$whereClouse = ($isShipped === false) ? 'B.Estate>0  AND B.Estate!=4' : 'B.Estate=0' ;
		$command = self::$db->createCommand('SELECT DISTINCT F.TypeId, F.TypeName 
												FROM data_in.yw1_salesmain A
												STRAIGHT_JOIN data_in.yw1_salessheet B ON A.OrderNumber=B.OrderNumber
												STRAIGHT_JOIN data_in.productdata E ON E.ProductId=B.ProductId
												STRAIGHT_JOIN data_in.producttype F ON F.TypeId=E.TypeId 
												WHERE '.$whereClouse.' 
												ORDER BY F.TypeId DESC');
		return $command->queryAll();
	}

	public function getSalesRecordByCustomer($customerId=0, $isShipped=false, $page=0, $pageSize=50, $EstateSTR=0, 
											 $productTypeId=0, $startDate='', $endDate='', 
											 $sortField='OrderDate', $sortDir='DESC', $keywords=''){

		$whereClouse = ($isShipped === false && $EstateSTR == 0) ? 'B.Estate>0  AND B.Estate!=4' : 'B.Estate=0' ;
		if($customerId > 0){
			$whereClouse .= ' AND G.CompanyId='.$customerId;
		}
		if($EstateSTR > 0){
			$whereClouse .= ' AND B.Estate='.$EstateSTR;
		}
		if($productTypeId > 0){
			$whereClouse .= ' AND E.TypeId='.$productTypeId;
		}
		if($keywords != ''){
			$keys = explode(' ', $keywords);
			$whereClouse .= ' AND (';
			for($i = count($keys) ; $i > 0 ; $i--){
				$whereClouse .= ' E.cName LIKE "%'.$keys[($i-1)].'%" OR';
			}
			$whereClouse = substr($whereClouse, 0, -2);
			$whereClouse .= ')';
		}
		if($startDate !== ''){
			$whereClouse .= ' AND A.OrderDate>="'.$startDate.'"';
		}
		if($endDate !== ''){
			$whereClouse .= ' AND A.OrderDate<="'.$endDate.'"';
		}

		$command = self::$db->createCommand('SELECT 
												A.OrderDate,A.ClientOrder,A.pistuffdata,A.picstatus,A.Operator,
												B.Id,B.OrderPO,B.POrderId,B.ProductId,B.Qty,B.Price,(B.Qty*B.Price) AS Amount,
												FORMAT((B.Qty*B.Price*H.Rate) ,2) as RMBamount,B.PackRemark,B.DeliveryDate,B.ShipType,B.Estate,B.Locks,
												C.PI,C.Leadtime,
												E.cName,E.eCode,E.Weight,E.TestStandard,E.pRemark,E.bjRemark,
												F.Name AS Unit,H.Rate,H.Symbol,K.Name AS CkName,B.CkId,
												G.Forshort, G.CompanyId
											FROM data_in.yw1_salesmain A
											LEFT JOIN data_in.yw1_salessheet B ON A.OrderNumber=B.OrderNumber
											LEFT JOIN data_in.yw3_pisheet C ON C.oId=B.Id
											LEFT JOIN data_in.yw2_orderexpress D ON D.POrderId=B.POrderId
											LEFT JOIN data_in.productdata E ON E.ProductId=B.ProductId
											LEFT JOIN data_public.packingunit F ON F.Id=E.PackingUnit 
											LEFT JOIN data_in.clientdata G ON A.CompanyId=G.CompanyId 
											LEFT JOIN data_public.currencydata H ON H.Id=G.Currency 
											LEFT JOIN data_public.product_ck K ON K.Id=B.CkId															  
											WHERE '.$whereClouse.' 
											ORDER BY '.$sortField.' '.$sortDir.'
											LIMIT '.($page*$pageSize).','.$pageSize);
		$records = $command->queryAll();
		return $records;
	}

	public function getSalesRecordByCustomerCount($customerId=0, $isShipped=false, $EstateSTR=0, $productTypeId=0,  
												  $startDate='', $endDate='', $keywords=''){

		$whereClouse = ($isShipped === false && $EstateSTR == 0) ? 'B.Estate>0  AND B.Estate!=4' : 'B.Estate=0' ;
		if($customerId > 0){
			$whereClouse .= ' AND A.CompanyId='.$customerId;
		}
		if($EstateSTR > 0){
			$whereClouse .= ' AND B.Estate='.$EstateSTR;
		}
		if($productTypeId > 0){
			$whereClouse .= ' AND E.TypeId='.$productTypeId;
		}
		if($keywords != ''){
			$keys = explode(' ', $keywords);
			$whereClouse .= ' AND (';
			for($i = count($keys) ; $i > 0 ; $i--){
				$whereClouse .= ' E.cName LIKE "%'.$keys[($i-1)].'%" OR';
			}
			$whereClouse = substr($whereClouse, 0, -2);
			$whereClouse .= ')';
		}
		if($startDate !== ''){
			$whereClouse .= ' AND A.OrderDate>="'.$startDate.'"';
		}
		if($endDate !== ''){
			$whereClouse .= ' AND A.OrderDate<="'.$endDate.'"';
		}
		$command = self::$db->createCommand('SELECT count(A.Id) AS count
											FROM data_in.yw1_salesmain A
											LEFT JOIN data_in.yw1_salessheet B ON A.OrderNumber=B.OrderNumber
											LEFT JOIN data_in.productdata E ON E.ProductId=B.ProductId
											WHERE '.$whereClouse);
		return $command->queryScalar();
	}

	public function getPriceQtyRMBAmountSum($customerId=0, $isShipped=false, $page=0, $pageSize=50, $EstateSTR=0, 
											 $productTypeId=0, $startDate='', $endDate='', 
											 $keywords=''){
		$whereClouse = ($isShipped === false && $EstateSTR == 0) ? 'B.Estate>0  AND B.Estate!=4' : 'B.Estate=0' ;
		if($customerId > 0){
			$whereClouse .= ' AND G.CompanyId='.$customerId;
		}
		if($EstateSTR > 0){
			$whereClouse .= ' AND B.Estate='.$EstateSTR;
		}
		if($productTypeId > 0){
			$whereClouse .= ' AND E.TypeId='.$productTypeId;
		}
		if($keywords != ''){
			$keys = explode(' ', $keywords);
			$whereClouse .= ' AND (';
			for($i = count($keys) ; $i > 0 ; $i--){
				$whereClouse .= ' E.cName LIKE "%'.$keys[($i-1)].'%" OR';
			}
			$whereClouse = substr($whereClouse, 0, -2);
			$whereClouse .= ')';
		}
		if($startDate !== ''){
			$whereClouse .= ' AND A.OrderDate>="'.$startDate.'"';
		}
		if($endDate !== ''){
			$whereClouse .= ' AND A.OrderDate<="'.$endDate.'"';
		}

		$command = self::$db->createCommand('SELECT 
												SUM(B.Qty) AS TotalQty,
												SUM(B.Price) AS TotalPrice,
												SUM((B.Qty*B.Price)) AS Amount,
												FORMAT(SUM(B.Qty*B.Price*H.Rate), 2) as RMBamount
											FROM data_in.yw1_salesmain A
											LEFT JOIN data_in.yw1_salessheet B ON A.OrderNumber=B.OrderNumber
											LEFT JOIN data_in.yw3_pisheet C ON C.oId=B.Id
											LEFT JOIN data_in.yw2_orderexpress D ON D.POrderId=B.POrderId
											LEFT JOIN data_in.productdata E ON E.ProductId=B.ProductId
											LEFT JOIN data_public.packingunit F ON F.Id=E.PackingUnit 
											LEFT JOIN data_in.clientdata G ON A.CompanyId=G.CompanyId 
											LEFT JOIN data_public.currencydata H ON H.Id=G.Currency 
											LEFT JOIN data_public.product_ck K ON K.Id=B.CkId															  
											WHERE '.$whereClouse);
		$records = $command->queryRow();
		return $records;
	}

	public function getSalesOrder($isShipped=false, $page=0, $pageSize=50){

		$whereClouse = ($isShipped === false) ? 'B.Estate>0  AND B.Estate!=4' : 'B.Estate=0' ;
		$command = self::$db->createCommand('SELECT 
												A.OrderDate,A.ClientOrder,A.pistuffdata,A.picstatus,A.Operator,
												B.Id,B.OrderPO,B.POrderId,B.ProductId,B.Qty,B.Price,(B.Qty*B.Price) AS Amount,
												FORMAT((B.Qty*B.Price*H.Rate), 2) as RMBamount,B.PackRemark,B.DeliveryDate,B.ShipType,B.Estate,B.Locks,
												C.PI,C.Leadtime,
												E.cName,E.eCode,E.Weight,E.TestStandard,E.pRemark,E.bjRemark,
												F.Name AS Unit,H.Rate,H.Symbol,K.Name AS CkName,B.CkId	
											FROM data_in.yw1_salesmain A
											LEFT JOIN data_in.yw1_salessheet B ON A.OrderNumber=B.OrderNumber
											LEFT JOIN data_in.yw3_pisheet C ON C.oId=B.Id
											LEFT JOIN data_in.yw2_orderexpress D ON D.POrderId=B.POrderId
											LEFT JOIN data_in.productdata E ON E.ProductId=B.ProductId
											LEFT JOIN data_public.packingunit F ON F.Id=E.PackingUnit 
											LEFT JOIN data_in.clientdata G ON A.CompanyId=G.CompanyId 
											LEFT JOIN data_public.currencydata H ON H.Id=G.Currency 
											LEFT JOIN data_public.product_ck K ON K.Id=B.CkId															  
											WHERE '.$whereClouse.' ORDER BY A.CompanyId,A.OrderDate DESC,A.Id DESC
											LIMIT '.($page*$pageSize).','.$pageSize);
		$records = $command->queryAll();

		//calcuate all the parameters
		$stockProductModel = new StockProduct();
		foreach($records as $idx=>$record){
			$productId = $record['ProductId'];
			$eCode = $record['eCode'];
			$scQty = $records[$idx]['scQty'] = $stockProductModel->getOnlineOrderSum($productId);
			$ddQty = $records[$idx]['ddQty'] = $stockProductModel->getSalesOrderSum($productId);
			$bpQty = $records[$idx]['bpQty'] = $stockProductModel->getTransferInBackupProductSum($productId);
			$bfQty = $records[$idx]['bfQty'] = $stockProductModel->getScrappedSum($productId);
			$rkQty = $records[$idx]['rkQty'] = $stockProductModel->getManufacturingSum($productId);
			$shipQty = $records[$idx]['shipQty'] = $stockProductModel->getShippedOutSum($productId);
			$ycshipQty = $records[$idx]['ycshipQty'] = $stockProductModel->getSuddenScrappedSum($productId);
			$bfswQty = $records[$idx]['bfswQty'] = $stockProductModel->getSuddenScrappedItemInStockSum($productId);
			$bfddQty = $records[$idx]['bfddQty'] = $stockProductModel->getSuddenScrappedOrderInStockSum($productId);
			$dcddQty = $records[$idx]['dcddQty'] = $stockProductModel->getTransferredOutStockOrderSum($productId);
			$drddQty = $records[$idx]['drddQty'] = $stockProductModel->getTransferredInStockOrderSum($productId);
			$cpdkqty = $records[$idx]['cpdkqty'] = $stockProductModel->getClearUnshippedOrderSum($productId);
			$tdinQty = $records[$idx]['tdinQty'] = $stockProductModel->getProductTurnoverInSum($productId);
			$tdoutQty = $records[$idx]['tdoutQty'] = $stockProductModel->getProductTurnoverOutSum($productId);
			$stpQty = $records[$idx]['stpQty'] = $stockProductModel->getFromStuffToProductSum($productId);
			$pthQty = $records[$idx]['pthQty'] = $stockProductModel->getBackupProductSum($productId);
			$records[$idx]['oStockQty'] = $scQty+$bpQty-$bfQty-$ddQty-$bfddQty+$cpdkqty-$dcddQty+$drddQty-$tdinQty+$tdoutQty;
			$records[$idx]['tStockQty'] = $rkQty+$bpQty-$bfQty-$shipQty-$bfswQty-$tdinQty+$tdoutQty+$stpQty-$pthQty;
			$records[$idx]['Price'] = sprintf("%.3f",$record['Price']);
			$records[$idx]['Amount'] = sprintf("%.3f",$record["Amount"]);
			$records[$idx]['RMBamount'] = sprintf("%.3f",$record["RMBamount"]);
			$costRow = $stockProductModel->getCost($productId);
			$price = $records[$idx]['Price'];
			$rate = $record['Rate'];//client's rate
			$qty = $record['Qty'];
			$records[$idx]['profitRMB'] = $profitRMB = sprintf("%.2f", $price * $rate - $costRow);//单品毛利，成本以默认BOM计算
			$records[$idx]['proficRMB_SUM'] = $profitRMB_SUM = $profitRMB * $qty;//订单毛利=单品毛利*订单数量
			$records[$idx]['profitRMBPC'] = $profitRMBPC = $price==0 ? 0 : sprintf("%.0f", ( $profitRMB * 100 ) / ($price * $rate));//单品毛利比率
			$qtyRecord = $stockProductModel->getPutOrderSumNCount($productId);
			$records[$idx]['AllQtySum'] = $AllQtySum = $qtyRecord['AllQty'];
			$records[$idx]['Orders'] = $Orders = $qtyRecord['Orders'];
			$records[$idx]['ShipQtySum'] = $ShipQtySum = $stockProductModel->getShippedSum($productId);
			$TempPC = ( $ShipQtySum / $AllQtySum ) * 100;
			$records[$idx]['TempPC'] = $TempPC>=1 ? ( round($TempPC)."%" ) : (sprintf("%.2f" ,$TempPC)."%");
			$records[$idx]['ReturnedQty'] = $ReturnedQty = $stockProductModel->getRefundSum($eCode);
			$records[$idx]['ReturnedPercent'] = $ReturnedPercent = $ShipQtySum > 0 ? (sprintf("%.1f",( ($ReturnedQty / $ShipQtySum) * 1000 ))) : 0;
		}

		return $records;
	}
}