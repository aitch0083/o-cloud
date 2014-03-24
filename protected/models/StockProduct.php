<?php

/*
	Scrap Types
	1. 报废
	2. 调用
	3. 出货(收费)
	4. 出货(不收费)
	5. 非生产性领料
	6. 退货
	7. 损耗
	8. 生产超领(收费)
	9. 重工
	10. 临时报废实物库存
	11. 临时报废订单库存
	12. 成品订单库存调出
	13. 被困还原不良

	Transfer Types:
	1. 返工入庫
	2. 備品入庫
	3. 其他入庫
	4. 临时成品转入
	5. 成品订单库存调入
	6. 客退入库
	7.被困还原
*/

class StockProduct extends CActiveRecord{

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
				throw new CDbException(Yii::t('yii', 'Unable to connect to DB:['.self::$targetDB.']'));
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
		return 'product_bp';
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

	public function getOnlineOrderSum($productId){
		//1 生产订单数量：根据成品配件关系，获取内部单产品的订单数量
		$command = self::$db->createCommand('SELECT IFNULL(SUM(A.Qty),0) AS scQty FROM data_in.yw1_ordersheet A WHERE A.ProductId=:productid');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['scQty'];
	}

	public function getSalesOrderSum($productId){
		//2 业务订单数量
		$command = self::$db->createCommand('SELECT IFNULL(SUM(A.Qty),0) AS ddQty FROM data_in.yw1_salessheet A WHERE A.ProductId=:productid and A.Estate !=9');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['ddQty'];	
	}

	public function getTransferInBackupProductSum($productId){
		//3 备品转入数量：加工费转入
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS bpQty FROM data_in.product_bp WHERE ProductId=:productid and type !=5');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['bpQty'];	
	}

	public function getScrappedSum($productId){
		//4 报废数量：加工费报废
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS bfQty FROM data_in.product_bf WHERE Estate=1 AND ProductId=:productid and Type not in(10,11,12)');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['bfQty'];		
	}

	public function getManufacturingSum($productId){
		//5 生产数量：根据成品配件关系，获取内部单产品的订单数量
		$command = self::$db->createCommand('SELECT  IFNULL(SUM(C.Qty),0) AS rkQty FROM data_in.pands A LEFT JOIN data_in.stuffdata B ON B.StuffId=A.StuffId LEFT JOIN data_in.ck1_rksheet C ON C.StuffId=B.StuffId WHERE A.ProductId=:productid AND (B.TypeId=9002 OR B.TypeId=9041)');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['rkQty'];		
	}

	public function getShippedOutSum($productId){
		//6 已出数量：订单状态为待出和已出
		$command = self::$db->createCommand('SELECT IFNULL(SUM(A.Qty),0) AS shipQty FROM data_in.ch1_shipsheet A WHERE A.ProductId=:productid');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['shipQty'];		
	}

	public function getSuddenScrappedSum($productId){
		//7 临时处理临时报废数据资料
		$command = self::$db->createCommand('SELECT IFNULL(SUM(A.Qty),0) AS shipQty FROM data_in.ch1_shipsheet A left join data_in.ch1_shipmain B on A.Mid=B.Id WHERE A.ProductId=:productid and B.Estate=0');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['shipQty'];		
	}

	public function getSuddenScrappedItemInStockSum($productId){
		//8 臨時報廢實物庫存
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS bfswQty FROM data_in.product_bf WHERE Estate=1 AND ProductId=:productid and Type=10');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['bfswQty'];		
	}	

	public function getSuddenScrappedOrderInStockSum($productId){
		//9 臨時報廢訂單庫存
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS bfddQty FROM data_in.product_bf WHERE Estate=1 AND ProductId=:productid and Type=11');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['bfddQty'];		
	}

	public function getTransferredOutStockOrderSum($productId){
		//10 成品订单库存调出
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS dcddQty FROM data_in.product_bf WHERE Estate=1 AND ProductId=:productid and Type=12');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['dcddQty'];		
	}

	public function getTransferredInStockOrderSum($productId){
		//11 成品订单库存调出
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS drddQty FROM data_in.product_bp WHERE ProductId=:productid and type=5');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['drddQty'];		
	}

	public function getClearUnshippedOrderSum($productId){
		//12 清除未出订单库存
		$command = self::$db->createCommand('SELECT IFNULL(sum(Qty),0) as cpdkqty from data_in.clear_product where ProductId=:productid');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['cpdkqty'];		
	}

	public function getProductTurnoverInSum($productId){
		//13 成品調入數量
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS tdinQty FROM data_in.product_td WHERE FromEstate=0 AND ProductId=:productid');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['tdinQty'];		
	}

	public function getProductTurnoverOutSum($productId){
		//14 成品調出數量
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS tdoutQty FROM data_in.product_td WHERE ToEstate=0 AND ProductId=:productid');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['tdoutQty'];		
	}

	public function getFromStuffToProductSum($productId){
		//15 配件到成品
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS stpQty FROM data_in.stufftoproduct WHERE  ProductId=:productid and Type=1');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['stpQty'];		
	}

	public function getBackupProductSum($productId){
		//16 備品數量
		$command = self::$db->createCommand('SELECT IFNULL(SUM(Qty),0) AS pthQty FROM data_in.product_thsheet WHERE  ProductId=:productid and Estate=0');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['pthQty'];		
	}

	public function getCost($productId){
		$command = self::$db->createCommand('SELECT IFNULL(SUM(B.Price*E.Rate*(SUBSTRING_INDEX(A.Relation ,\'/\',1)/SUBSTRING_INDEX(A.Relation,\'/\',-1))),0) AS oTheCost
			FROM data_in.pands A
			LEFT JOIN data_in.stuffdata B ON B.StuffId=A.StuffId
			LEFT JOIN data_in.bps C ON C.StuffId=B.StuffId
			LEFT JOIN data_in.providerdata D ON D.CompanyId=C.CompanyId
			LEFT JOIN data_public.currencydata E ON E.Id=D.Currency
			WHERE A.ProductId=:productid ORDER BY A.Id DESC');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();

		return $result['oTheCost'];
	}

	public function getPutOrderSumNCount($productId){
		$record = array(
			'AllQty' => 0,
			'Orders' => 0
		);
		
		//下單總數
		$command = self::$db->createCommand('SELECT SUM(AllQty) AS ALLQTY, COUNT(*) AS Orders FROM ( SELECT SUM(Qty) AS AllQty FROM data_in.yw1_salessheet WHERE ProductId=:productid GROUP BY OrderPO ) A');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		$record['AllQty'] = $result['ALLQTY'];
		$record['Orders'] = $result['Orders'];

		return $record;
	}

	public function getShippedSum($productId){
		$command = self::$db->createCommand('SELECT SUM(Qty) AS ShipQty FROM data_in.ch1_shipsheet WHERE ProductId=:productid');
		$command->bindParam('productid', $productId);
		$result = $command->queryRow();
		return $result['ShipQty'];	
	}

	public function getRefundSum($eCode){
		$command = self::$db->createCommand('SELECT SUM(Qty) AS ReturnedQty FROM data_in.product_returned WHERE eCode=:ecode');
		$command->bindParam('ecode', $eCode);
		$result = $command->queryRow();
		return $result['ReturnedQty'];	
	}
}