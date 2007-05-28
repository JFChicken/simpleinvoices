<?php

$conn = mysql_connect( $db_host, $db_user, $db_password );
mysql_select_db( $db_name, $conn );
$mysql = substr(mysql_get_server_info(),0,1)+0;


/**
 * Used for logging all queries
 */
function mysqlQuery($sqlQuery) {
	$logging = 1; //Set to 1 to enable (for testing...)
	$pattern = "/[^a-z]*select/i";
	$userid = 1;

	if($query = mysql_query($sqlQuery)) {
		if($logging && (preg_match($pattern,$sqlQuery) == 0)) {
			$sql = "INSERT INTO  `si_log` (`timestamp` ,  `userid` ,  `sqlquerie`, `last_id` ) VALUES (CURRENT_TIMESTAMP ,  '$userid',  '". addslashes (preg_replace('/\s\s+/', ' ', trim($sqlQuery)))."','".mysql_insert_id()."');";
			mysql_unbuffered_query($sql);
		}
		return $query;
	}
	else {
		echo $sqlQuery."<br />".mysql_error();
	}
}

function getCustomer($id) {
	global $tb_prefix;
	$print_customer = "SELECT * FROM {$tb_prefix}customers WHERE id = $id";
	$result_print_customer = mysqlQuery($print_customer) or die(mysql_error());
	return mysql_fetch_array($result_print_customer);
}

function getBiller($id) {
	global $tb_prefix;
	global $LANG;
	$print_biller = "SELECT * FROM {$tb_prefix}biller WHERE id = $id";
	$result_print_biller = mysqlQuery($print_biller) or die(mysql_error());
	$biller = mysql_fetch_array($result_print_biller);
	$biller['wording_for_enabled'] = $biller['enabled']==1?$LANG['enabled']:$LANG['disabled'];
	return $biller;
}

function getPreference($id) {
	global $tb_prefix;
	global $LANG;
	$print_preferences = "SELECT * FROM {$tb_prefix}preferences WHERE pref_id = $id";
	$result_print_preferences  = mysqlQuery($print_preferences) or die(mysql_error());
	$preference = mysql_fetch_array($result_print_preferences);
	$preference['enabled'] = $preference['pref_enabled']==1?$LANG['enabled']:$LANG['disabled'];
	return $preference;
}

function getSQLPatches() {
	global $tb_prefix;
	$sql = "SELECT * FROM {$tb_prefix}sql_patchmanager ORDER BY sql_release";                  
	$query = mysqlQuery($sql) or die(mysql_error());

	$patches = null;
	
	for($i=0;$patch = mysql_fetch_array($query);$i++) {
		$patches[$i] = $patch;
	}
	return $patches;
}

function getPreferences() {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}preferences ORDER BY pref_description";
	$query  = mysqlQuery($sql) or die(mysql_error());
	
	$preferences = null;
	
	for($i=0;$preference = mysql_fetch_array($query);$i++) {
		
  		if ($preference['pref_enabled'] == 1) {
  			$preference['enabled'] = $LANG['enabled'];
  		} else {
  			$preference['enabled'] = $LANG['disabled'];
  		}

		$preferences[$i] = $preference;
	}
	
	return $preferences;
}

function getActiveTaxes() {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}tax WHERE tax_enabled != 0 ORDER BY tax_description";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	$taxes = null;
	
	for($i=0;$tax = mysql_fetch_array($query);$i++) {
		if ($tax['tax_enabled'] == 1) {
			$tax['enabled'] = $LANG['enabled'];
		} else {
			$tax['enabled'] = $LANG['disabled'];
		}

		$taxes[$i] = $tax;
	}
	
	return $taxes;
}

function getActivePreferences() {
	global $tb_prefix;
	
	$sql = "SELECT * FROM {$tb_prefix}preferences WHERE pref_enabled ORDER BY pref_description";
	$query  = mysqlQuery($sql) or die(mysql_error());
	
	$preferences = null;
	
	for($i=0;$preference = mysql_fetch_array($query);$i++) {
		$preferences[$i] = $preference;
	}
	
	return $preferences;
}

function getCustomFieldLabels() {
	global $LANG;
	global $tb_prefix;
	
	$sql = "SELECT * FROM {$tb_prefix}custom_fields ORDER BY cf_custom_field";
	$result = mysqlQuery($sql) or die(mysql_error());
	
	for($i=0;$customField = mysql_fetch_array($result);$i++) {
		$customFields[$customField['cf_custom_field']] = $customField['cf_custom_label'];

		if($customFields[$customField['cf_custom_field']] == null) {
			//If not set, don't show...
			$customFields[$customField['cf_custom_field']] = $LANG["custom_field"].' '.($i%4+1);
		}
	}

	return $customFields;
}
 

function getBillers() {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}biller ORDER BY name";
	$query  = mysqlQuery($sql) or die(mysql_error());
	
	$billers = null;
	
	for($i=0;$biller = mysql_fetch_array($query);$i++) {
		
  		if ($biller['enabled'] == 1) {
  			$biller['enabled'] = $LANG['enabled'];
  		} else {
  			$biller['enabled'] = $LANG['disabled'];
  		}
		$billers[$i] = $biller;
	}
	
	return $billers;
}

function getActiveBillers() {
	global $tb_prefix;
	$sql = "SELECT * FROM {$tb_prefix}biller WHERE enabled != 0 ORDER BY name";
	$query = mysqlQuery($sql) or die(mysql_error());
		
	$billers = null;
	
	for($i=0;$biller = mysql_fetch_array($query);$i++) {
		$billers[$i] = $biller;
	}
	
	return $billers;
}



function getTaxRate($id) {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}tax WHERE tax_id = $id";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	$tax = mysql_fetch_array($query);
	$tax['enabled'] = $tax['tax_enabled'] == 1 ? $LANG['enabled']:$LANG['disabled'];
	
	return $tax;
}

function getPaymentType($id) {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}payment_types WHERE pt_id = $id";
	$query = mysqlQuery($sql) or die(mysql_error());
	$paymentType = mysql_fetch_array($query);
	$paymentType['enabled'] = $paymentType['pt_enabled']==1?$LANG['enabled']:$LANG['disabled'];
	
	return $paymentType;
}

function getPaymentTypes() {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}payment_types ORDER BY pt_description";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	$paymentTypes = null;

	for ($i=0;$paymentType = mysql_fetch_array($query);$i++) {
		if ($paymentType['pt_enabled'] == 1) {
			$paymentType['pt_enabled'] = $LANG['enabled'];
		} else {
			$paymentType['pt_enabled'] = $LANG['disabled'];
		}
		$paymentTypes[$i]=$paymentType;
	}
	
	return $paymentTypes;
}

function getProduct($id) {
	global $tb_prefix;
	global $LANG;
	$sql = "SELECT * FROM {$tb_prefix}products WHERE id = $id";
	$query = mysqlQuery($sql) or die(mysql_error());
	$product = mysql_fetch_array($query);
	$product['wording_for_enabled'] = $product['enabled']==1?$LANG['enabled']:$LANG['disabled'];
	return $product;
}

function getProducts() {
	
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}products WHERE visible = 1 ORDER BY description";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	$products = null;
	
	for($i=0;$product = mysql_fetch_array($query);$i++) {
		
		if ($product['enabled'] == 1) {
			$product['enabled'] = $LANG['enabled'];
		} else {
			$product['enabled'] = $LANG['disabled'];
		}

		$products[$i] = $product;
	}
	
	return $products;
}

function getActiveProducts() {
	
	global $tb_prefix;
	
	$sql = "SELECT * FROM {$tb_prefix}products WHERE enabled != 0 ORDER BY description";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	$products = null;
	
	for($i=0;$product = mysql_fetch_array($query);$i++) {
		$products[$i] = $product;
	}
	
	return $products;
}


function getTaxes() {
	global $tb_prefix;
	global $LANG;
	
	$sql = "SELECT * FROM {$tb_prefix}tax ORDER BY tax_description";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	$taxes = null;
	
	for($i=0;$tax = mysql_fetch_array($query);$i++) {
		if ($tax['tax_enabled'] == 1) {
			$tax['enabled'] = $LANG['enabled'];
		} else {
			$tax['enabled'] = $LANG['disabled'];
		}

		$taxes[$i] = $tax;
	}
	
	return $taxes;
}


function getDefaultCustomer() {
	global $tb_prefix;
	$sql = "SELECT *,c.name AS name FROM {$tb_prefix}customers c, {$tb_prefix}system_defaults s WHERE ( s.name = 'customer' AND c.id = s.value)";
	$query = mysqlQuery($sql) or die(mysql_error());
	return mysql_fetch_array($query);
}

function getDefaultPaymentType() {
	global $tb_prefix;
	$sql = "SELECT * FROM {$tb_prefix}payment_types p, {$tb_prefix}system_defaults s WHERE ( s.name = 'payment_type' AND p.pt_id = s.value)";
	$query = mysqlQuery($sql) or die(mysql_error());
	return mysql_fetch_array($query);
}

function getDefaultPreference() {
	global $tb_prefix;
	$sql = "SELECT * FROM {$tb_prefix}preferences p, {$tb_prefix}system_defaults s WHERE ( s.name = 'preference' AND p.pref_id = s.value)";
	$query = mysqlQuery($sql) or die(mysql_error());
	return mysql_fetch_array($query);
}

function getDefaultBiller() {
	global $tb_prefix;
	$sql = "SELECT *,b.name AS name FROM {$tb_prefix}biller b, {$tb_prefix}system_defaults s WHERE ( s.name = 'biller' AND b.id = s.value )";
	$query = mysqlQuery($sql) or die(mysql_error());
	return mysql_fetch_array($query);
}


function getDefaultTax() {
	global $tb_prefix;
	$sql = "SELECT * FROM {$tb_prefix}tax t, {$tb_prefix}system_defaults s WHERE (s.name = 'tax' AND t.tax_id = s.value)";
	$query = mysqlQuery($sql) or die(mysql_error());
	return mysql_fetch_array($query);
}

function getInvoiceTotal($invoice_id) {
	global $LANG;
	global $tb_prefix;
	
	$sql ="SELECT SUM(total) AS total FROM {$tb_prefix}invoice_items WHERE invoice_id = $invoice_id";
	$query = mysqlQuery($sql);
	$res = mysql_fetch_array($query);
	//echo "TOTAL".$res['total'];
	return $res['total'];
}

function getInvoice($id) {
	global $tb_prefix;
	global $config;
	
	$sql = "SELECT * FROM {$tb_prefix}invoices WHERE id = $id";
	$query  = mysqlQuery($sql) or die(mysql_error());

	$invoice = mysql_fetch_array($query);
	$invoice['date'] = date( $config['date_format'], strtotime( $invoice['date'] ) );
	$invoice['calc_date'] = date('Y-m-d', strtotime( $invoice['date'] ) );
	$invoice['total'] = getInvoiceTotal($invoice['id']);
	$invoice['total_format'] = number_format($invoice['total'],2);
	$invoice['paid'] = calc_invoice_paid($invoice['id']);
	$invoice['paid_format'] = number_format($invoice['paid'],2);
	$invoice['owing'] = number_format($invoice['total'] - $invoice['paid'],2);

	
	#invoice total tax
	$sql ="SELECT SUM(tax_amount) AS total_tax, SUM(total) AS total FROM {$tb_prefix}invoice_items WHERE invoice_id =$id";
	$query = mysqlQuery($sql) or die(mysql_error());
	$result = mysql_fetch_array($query);
	//$invoice['total'] = number_format($result['total'],2);
	$invoice['total_tax'] = number_format($result['total_tax'],2);
	
	return $invoice;
}


function getInvoiceItems($id) {
	global $tb_prefix;
	$sql = "SELECT * FROM {$tb_prefix}invoice_items WHERE invoice_id =$id";
	$query = mysqlQuery($sql);
	
	$invoiceItems = null;
	
	for($i=0;$invoiceItem = mysql_fetch_array($query);$i++) {
	
		$invoiceItem['quantity_formatted'] = number_format($invoiceItem['quantity'],2);
		$invoiceItem['unit_price'] = number_format($invoiceItem['unit_price'],2);
		$invoiceItem['tax_amount'] = number_format($invoiceItem['tax_amount'],2);
		$invoiceItem['gross_total'] = number_format($invoiceItem['gross_total'],2);
		$invoiceItem['total'] = number_format($invoiceItem['total'],2);
		
		$sql = "SELECT * FROM {$tb_prefix}products WHERE id = {$invoiceItem['product_id']}";
		$query2 = mysqlQuery($sql) or die(mysql_error());
		$invoiceItem['product'] = mysql_fetch_array($query2);	
		
		$invoiceItems[$i] = $invoiceItem;
	}
	
	return $invoiceItems;
}


function getSystemDefaults() {
	global $tb_prefix;
	$print_defaults = "SELECT * FROM {$tb_prefix}system_defaults";
	$result_print_defaults = mysqlQuery($print_defaults) or die(mysql_error());
	
	$defaults = null;
	$default = null;
	
	
	while($default = mysql_fetch_array($result_print_defaults)) {
		$defaults["$default[name]"] = $default['value'];
	}

	return $defaults;
}

function updateDefault($name,$value) {
	global $tb_prefix;
	$sql = "UPDATE {$tb_prefix}system_defaults SET `value` =  '$value' WHERE  `name` = '$name'"; 
	//echo $sql;
	if (mysqlQuery($sql)) {
		return true;
	}
	return false;
}

function getInvoiceType($id) {
	global $tb_prefix;
	$sql = "SELECT inv_ty_description FROM {$tb_prefix}invoice_type WHERE inv_ty_id = $id";
	$query = mysqlQuery($sql) or die(mysql_error());
	return mysql_fetch_array($query);
}

function insertBiller() {
	global $tb_prefix;
	$sql = "INSERT into
			{$tb_prefix}biller
		VALUES
			(
				'',
				'$_POST[name]',
				'$_POST[street_address]',
				'$_POST[street_address2]',
				'$_POST[city]',
				'$_POST[state]',
				'$_POST[zip_code]',
				'$_POST[country]',
				'$_POST[phone]',
				'$_POST[mobile_phone]',
				'$_POST[fax]',
				'$_POST[email]',
				'$_POST[logo]',
				'$_POST[footer]',
				'$_POST[notes]',
				'$_POST[custom_field1]',
				'$_POST[custom_field2]',
				'$_POST[custom_field3]',
				'$_POST[custom_field4]',
				'$_POST[enabled]'
			 )";

	return mysqlQuery($sql);
	
}

function updateBiller() {
	global $tb_prefix;
	$sql = "UPDATE
				{$tb_prefix}biller
			SET
				name = '$_POST[name]',
				street_address = '$_POST[street_address]',
				street_address2 = '$_POST[street_address2]',
				city = '$_POST[city]',
				state = '$_POST[state]',
				zip_code = '$_POST[zip_code]',
				country = '$_POST[country]',
				phone = '$_POST[phone]',
				mobile_phone = '$_POST[mobile_phone]',
				fax = '$_POST[fax]',
				email = '$_POST[email]',
				logo = '$_POST[logo]',
				footer = '$_POST[footer]',
				notes = '$_POST[notes]',
				custom_field1 = '$_POST[custom_field1]',
				custom_field2 = '$_POST[custom_field2]',
				custom_field3 = '$_POST[custom_field3]',
				custom_field4 = '$_POST[custom_field4]',
				enabled = '$_POST[enabled]'
			WHERE
				id = '$_GET[submit]'";
	return mysqlQuery($sql);
}

function updateCustomer() {
	global $tb_prefix;

	$sql = "
			UPDATE
				{$tb_prefix}customers
			SET
				name = '$_POST[name]',
				attention = '$_POST[attention]',
				street_address = '$_POST[street_address]',
				street_address2 = '$_POST[street_address2]',
				city = '$_POST[city]',
				state = '$_POST[state]',
				zip_code = '$_POST[zip_code]',
				country = '$_POST[country]',
				phone = '$_POST[phone]',
				mobile_phone = '$_POST[mobile_phone]',
				fax = '$_POST[fax]',
				email = '$_POST[email]',
				notes = '$_POST[notes]',
				custom_field1 = '$_POST[custom_field1]',
				custom_field2 = '$_POST[custom_field2]',
				custom_field3 = '$_POST[custom_field3]',
				custom_field4 = '$_POST[custom_field4]',
				enabled = '$_POST[enabled]'
			WHERE
				id = " . $_GET['submit'];

	return mysqlQuery($sql);
}

function insertCustomer() {
	global $tb_prefix;
	extract( $_POST );
	$sql = "INSERT INTO {$tb_prefix}customers VALUES ('','$attention', '$name','$street_address','$street_address2','$city','$state',' $zip_code','$country','$phone', '$mobile_phone', '$fax', '$email', '$notes', '$custom_field1', '$custom_field2', '$custom_field3', '$custom_field4', '$enabled')";
	
	return mysqlQuery($sql);
	
}
		
		

function getInvoices(&$query) {
	global $config;
	$invoice = null;

	if($invoice =  mysql_fetch_array($query)) {

		$invoice['calc_date'] = date( 'Y-m-d', strtotime( $invoice['date'] ) );
		$invoice['date'] = date( $config['date_format'], strtotime( $invoice['date'] ) );
			
		#invoice total total - start
		$invoice['total'] = getInvoiceTotal($invoice['id']);
		$invoice['total_format'] = number_format($invoice['total'],2);
		#invoice total total - end
		
		#amount paid calc - start
		$invoice['paid'] = calc_invoice_paid($invoice['id']);
		$invoice['paid_format'] = number_format($invoice['paid'],2);
		#amount paid calc - end
		
		#amount owing calc - start
		$invoice['owing'] = $invoice['total'] - $invoice['paid'];
		$invoice['owing_format'] = number_format($invoice['total'] - $invoice['paid'],2);
		#amount owing calc - end
	}
	return $invoice;
}

function getCustomerInvoices($id) {
	global $tb_prefix;
	$invoices = null;
	
	$sql = "SELECT * FROM {$tb_prefix}invoices WHERE customer_id =$id  ORDER BY id DESC";
	$query = mysqlQuery($sql) or die(mysql_error());
	
	for($i = 0;$invoice = getInvoices($query);$i++) {
		$invoices[$i] = $invoice;
	}
	
	return $invoices;

}

function getCustomers() {
		
	global $LANG;
	global $tb_prefix;
	$customer = null;
	
	$sql = "SELECT * FROM {$tb_prefix}customers ORDER BY name";
	$result = mysqlQuery($sql) or die(mysql_error());

	$customers = null;

	for($i=0;$customer = mysql_fetch_array($result);$i++) {
		if ($customer['enabled'] == 1) {
			$customer['enabled'] = $LANG['enabled'];
		} else {
			$customer['enabled'] = $LANG['disabled'];
		}

		#invoice total calc - start
		$customer['total'] = calc_customer_total($customer['id']);
		#invoice total calc - end

		#amount paid calc - start
		$customer['paid'] = calc_customer_paid($customer['id']);
		#amount paid calc - end

		#amount owing calc - start
		$customer['owing'] = $customer['total'] - $customer['paid'];
		
		#amount owing calc - end
		$customers[$i] = $customer;

	}
	
	return $customers;
}

function getActiveCustomers() {
		
	global $LANG;
	global $tb_prefix;
	
	$sql = "SELECT * FROM {$tb_prefix}customers WHERE enabled != 0 ORDER BY name";
	$result = mysqlQuery($sql) or die(mysql_error());

	$customers = null;

	for($i=0;$customer = mysql_fetch_array($result);$i++) {
		$customers[$i] = $customer;
	}
	
	return $customers;
}

function insertInvoice($type) {
	global $tb_prefix;
	
	$sql = "INSERT 
			into
		{$tb_prefix}invoices (
			id, 
			biller_id, 
			customer_id, 
			type_id,
			preference_id, 
			date, 
			note,
			custom_field1,
			custom_field2,
			custom_field3,
			custom_field4
		)
		VALUES
		(
			'',
			'$_POST[biller_id]',
			'$_POST[customer_id]',
			'$type',
			'$_POST[preference_id]',
			'$_POST[date]',
			'$_POST[note]',
			'$_POST[customField1]',
			'$_POST[customField2]',
			'$_POST[customField3]',
			'$_POST[customField4]'
			)";
	
	//echo $sql."<br />";
	
	return mysqlQuery($sql);
}

function updateInvoice($invoice_id) {
	global $tb_prefix;
		$sql = "UPDATE
			{$tb_prefix}invoices
		SET
			biller_id = '$_POST[biller_id]',
			customer_id = '$_POST[customer_id]',
			preference_id = '$_POST[preference_id]',
			date = '$_POST[date]',
			note = '$_POST[note]',
			custom_field1 = '$_POST[customField1]',
			custom_field2 = '$_POST[customField2]',
			custom_field3 = '$_POST[customField3]',
			custom_field4 = '$_POST[customField4]'
		WHERE
			id = $invoice_id";
			
	return mysqlQuery($sql);
}

function insertInvoiceItem($invoice_id,$quantity,$product_id,$tax_id,$description) {
	global $tb_prefix;
	
	$tax = getTaxRate($tax_id);
	$product = getProduct($product_id);
	print_r($product);
	$actual_tax = $tax['tax_percentage']  / 100 ;
	$total_invoice_item_tax = $product['unit_price'] * $actual_tax;
	$tax_amount = $total_invoice_item_tax * $quantity;
	$total_invoice_item = $total_invoice_item_tax + $product['unit_price'] ;	
	$total = $total_invoice_item * $quantity;
	$gross_total = $product['unit_price']  * $quantity;
	
	$sql = "INSERT INTO {$tb_prefix}invoice_items (`invoice_id`,`quantity`,`product_id`,`unit_price`,`tax_id`,`tax`,`tax_amount`,`gross_total`,`description`,`total`) VALUES ($invoice_id,$quantity,$product_id,$product[unit_price],'$tax[tax_id]',$tax[tax_percentage],$tax_amount,$gross_total,'$description',$total)";

	//echo $sql;
	return mysqlQuery($sql);

}

function updateInvoiceItem($id,$quantity,$product_id,$tax_id,$description) {

	global $tb_prefix;
	
	$product = getProduct($product_id);
	$tax = getTaxRate($tax_id);
	
	$total_invoice_item_tax = $product['unit_price'] * $tax['tax_percentage'] / 100;	//:100?
	$tax_amount = $total_invoice_item_tax * $quantity;
	$total_invoice_item = $total_invoice_item_tax + $product['unit_price'];
	$total = $total_invoice_item * $quantity;
	$gross_total = $product['unit_price'] * $quantity;
	
	global $tb_prefix;
	
	$sql = "UPDATE {$tb_prefix}invoice_items 
	SET `quantity` =  '$quantity',
	`product_id` = '$product_id',
	`unit_price` = '$product[unit_price]',
	`tax_id` = '$tax_id',
	`tax` = '$tax[tax_percentage]',
	`tax_amount` = '$tax_amount',
	`gross_total` = '$gross_total',
	`description` = '$description',
	`total` = '$total'			
	WHERE  `id` = '$id'";
	
	//echo $sql;
		
	return mysqlQuery($sql);
}

function getMenuStructure() {
	$sql = "SELECT * FROM  `si_menu` WHERE enabled = 1 ORDER BY parentid,  `order`";
	$query = mysqlQuery($sql) or die(mysql_error());
	$menu = null;
	
	while($res = mysql_fetch_array($query)) {
		$menu[$res['parentid']][$res['id']]["name"] = $res['name'];
		$menu[$res['parentid']][$res['id']]["link"] = $res['link'];
		$menu[$res['parentid']][$res['id']]["id"] = $res['id'];
	}
		
	//printEntries($menu,0,1);
	//return $menu;
}

function printEntries($menu,$id,$depth) {
	
	foreach($menu[$id] as $tempentrie) {
		//echo $id;
		echo "Name:".$tempentrie["name"]."<br />";
		echo "ID:".$tempentrie["id"]."<br />";
		echo "DEPTH: ".$depth;
		echo "<br /><br />";
		
		if(isset($menu[$tempentrie["id"]])) {
			printEntries($menu,$tempentrie["id"],$depth+1);
		}
	}
}
	
//in this file are functions for all sql queries
?>
