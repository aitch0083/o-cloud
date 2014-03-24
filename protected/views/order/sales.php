<div class="navbar navbar-default">
	<div class="container-fluid">
		<form id="TargetFilterForm" class="navbar-form navbar-left" role="search" action="/order/sales" method="get">
			<input type="hidden" name="page" value="<?php echo $page; ?>"/>
			<input type="hidden" name="pageSize" value="<?php echo $pageSize; ?>"/>
			<div class="form-group">
			    <label><b class="glyphicon glyphicon-plane"></b> <?php echo Yii::t('yii', '出貨') ?>: </label>
				<select name="isShipped" id="FilterCustomer">
					<option value="false" <?php echo $isShipped ? '' : 'selected'; ?>>未出貨</option>
					<option value="true" <?php echo $isShipped ? 'selected' : ''; ?>>已出貨</option>
				</select>
		  	</div>
			<div class="form-group">
			    <label><b class="glyphicon glyphicon-heart"></b> <?php echo Yii::t('yii', '客戶') ?>: </label>
				<select name="customerId" id="FilterCustomer">
					<option value="0" <?php echo $customerId == 0 ? 'selected' : '' ?>>全部...</option>
					<?php foreach($customers as $idx=>$customer): ?>
					<option value="<?php echo $customer['CompanyId']; ?>" <?php echo $customerId == $customer['CompanyId'] ? 'selected' : '' ?> >
						<?php echo $customer['Forshort']; ?>
					</option>
					<?php endforeach; ?>
				</select>
		  	</div>
			<div class="form-group">
			    <label><b class="glyphicon glyphicon-barcode"></b> <?php echo Yii::t('yii', '生產') ?>:</label>
				<select name="EstateSTR" id="EstateSTR" onchange="ResetPage(this.name)">
					<option value="0" <?php echo $EstateSTR == 0 ? 'selected' : '' ?> >全部...</option>
					<option value="1" <?php echo $EstateSTR == 1 ? 'selected' : '' ?>>未生產已確認</option>
					<option value="9" <?php echo $EstateSTR == 9 ? 'selected' : '' ?>>未生產未確認</option>
				</select>
		  	</div>
		  	<div class="form-group">
			    <label><b class="glyphicon glyphicon-list-alt"></b> <?php echo Yii::t('yii', '產品類型') ?>:</label>
				<select name="productTypeId" id="FilterCustomer">
					<option value="">全部...</option>
					<?php foreach($customerOrderTypes as $idx=>$type): ?>
					<option value="<?php echo $type['TypeId']; ?>" <?php echo $type['TypeId'] === $productTypeId ? 'selected' : ''; ?> >
						<?php echo $type['TypeName']; ?>
					</option>
					<?php endforeach; ?>
				</select>
		  	</div>
		  	<div class="form-group">
			    <label><b class="glyphicon glyphicon-list"></b> <?php echo Yii::t('yii', '顯示筆數') ?>:</label>
				<select name="pageSize" id="FilterCustomer">
					<option value="30" <?php echo $pageSize == 30 ? 'selected' : '' ?> >30</option>
					<option value="50" <?php echo $pageSize == 50 ? 'selected' : '' ?> >50</option>
					<option value="100" <?php echo $pageSize == 100 ? 'selected' : '' ?> >100</option>
					<option value="150" <?php echo $pageSize == 150 ? 'selected' : '' ?> >150</option>
				</select>
		  	</div>
		  	<div class="form-group">
			    <label><b class="glyphicon glyphicon-sort"></b> <?php echo Yii::t('yii', '排序') ?>:</label>
				<select name="sortField" id="FilterCustomer">
					<?php foreach($columns as $key=>$column): ?>
					<option value="<?php echo $key ?>" <?php echo  $sortField === $key ? 'selected' : '' ?> ><?php echo $column['label']; ?></option>
					<?php endforeach; ?>
				</select>
				<select name="sortDir" id="FilterCustomer">
					<option value="ASC" <?php echo $sortDir === 'ASC' ? 'selected' : ''; ?> >昇冪</option>
					<option value="DESC" <?php echo $sortDir === 'DESC' ? 'selected' : ''; ?> >降冪</option>
				</select>
		  	</div>
		  	<div class="form-group">
			    <label><b class="glyphicon glyphicon-calendar"></b> <?php echo Yii::t('yii', '下單區間') ?>:</label>
			    <input type="text" name="startDate" class="form-control input-sm date-field" placeholder="開始時間" value="<?php echo $startDate; ?>" maxlength="10"> ~ 
			    <input type="text" name="endDate" class="form-control input-sm date-field" placeholder="結束時間" value="<?php echo $endDate; ?>" maxlength="10">
		  	</div>
		  	<div class="form-group">
		  		<label><b class="glyphicon glyphicon-search"></b> <?php echo Yii::t('yii', '搜尋') ?>:</label>
		        <input type="text" name="keywords" class="form-control input-sm" placeholder="Keywords..." value="<?php echo $keywords; ?>" maxlength="20">
		  	</div>
		  	<button class="btn btn-xs" type="submit"><span class="glyphicon glyphicon-play"></span></button>
		</form>	
	</div>
</div>

<div>
	<div class="panel panel-default">
		<div class="panel-header">
			<h3>
				<?php echo $isShipped === false ? '未' : '已' ?>出貨業務訂單
				<small>Records:<?php echo $count; ?> rows, page: <?php echo $page+1; ?>, <?php echo $pageSize; ?> rows/page.</small>
			</h3>
			<ul class="list-inline">
				<li><b class="glyphicon glyphicon-pushpin"></b> 本次查詢結果&gt;&gt;</li>
				<li>總量：<?php echo $getPriceQtyRMBAmountSum['TotalQty']; ?></li>
				<li>總價值：<?php echo $getPriceQtyRMBAmountSum['TotalPrice']; ?></li>
				<li>總銷量（USD）：$<?php echo $getPriceQtyRMBAmountSum['Amount']; ?></li>
				<li>總銷量（RMB）：¥<?php echo $getPriceQtyRMBAmountSum['RMBamount']; ?></li>
			</ul>
		</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered table-hover table-condensed">
				<thead>
					<tr class="info">
						<td colspan="9" class="text-center">
							<ul class="pagination">
								<li <?php echo $page <= 0 ? 'class="disabled"' : '' ?>>
									<a href="#" cmdVal="<?php echo $page - 1 <= 0 ? 0 : $page - 1; ?>" target="TargetFilterForm">&laquo;</a>
								</li>
								<?php for($i = 0 ; $i < $pageNum ; $i++): ?>
								<li <?php echo $i == $page ? 'class="active"' : ''; ?> >
									<a href="#" class="paginationBtn" cmdVal="<?php echo $i ?>" target="TargetFilterForm"><?php echo $i+1; ?></a>
								</li>
								<?php endfor; ?>
								<li <?php echo $page + 1 >= $pageNum ? 'class="disabled"' : '' ?>>
									<a href="#" cmdVal="<?php echo $page + 1 >= $pageNum ? $pageNum - 1 : $page + 1; ?>" target="TargetFilterForm">&raquo;</a>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th>ID</th>
						<?php foreach($columns as $idx=>$column): ?>
						<th><?php echo $column['label']; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($salesOrders as $idx=>$order): ?>
					<tr>
						<td><?php echo $page * $pageSize + $idx + 1; ?></td>
						<?php foreach($columns as $colKey=>$column): ?>
						<td class="<?php echo isset($column['class']) ? $column['class'] : ''; ?>">
							<?php echo $order[$colKey]; ?>
						</td>
						<?php endforeach; ?>
					</tr>
					<?php endforeach; ?>
					<tr class="info">
						<td colspan="9" class="text-center">
							<ul class="pagination">
								<li <?php echo $page <= 0 ? 'class="disabled"' : '' ?>>
									<a href="#" cmdVal="<?php echo $page - 1 <= 0 ? 0 : $page - 1; ?>" target="TargetFilterForm">&laquo;</a>
								</li>
								<?php for($i = 0 ; $i < $pageNum ; $i++): ?>
								<li <?php echo $i == $page ? 'class="active"' : ''; ?> >
									<a href="#" class="paginationBtn" cmdVal="<?php echo $i ?>" target="TargetFilterForm"><?php echo $i+1; ?></a>
								</li>
								<?php endfor; ?>
								<li <?php echo $page + 1 >= $pageNum ? 'class="disabled"' : '' ?>>
									<a href="#" cmdVal="<?php echo $page + 1 >= $pageNum ? $pageNum - 1 : $page + 1; ?>" target="TargetFilterForm">&raquo;</a>
								</li>
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>