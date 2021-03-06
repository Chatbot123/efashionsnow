<?php
$method = $_SERVER['REQUEST_METHOD'];
//process only when method id post
if($method == 'POST')
{
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	
//------------------------------------
//--Vendor balance detail starts here
//-------------------------------------
if($json->queryResult->intent->displayName=='OPPVendorBalSet')
{
	if(isset($json->queryResult->parameters->CompCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
	if(isset($json->queryResult->parameters->VendorCode))
	{ 
		$v_VendorCode = $json->queryResult->parameters->VendorCode; 
	  	$v_VendorCode = strtoupper($v_VendorCode);
	}
	
	
	
$curl = curl_init();
//$api_request_parameters = array('filter'=>"((Vendor eq '".$v_VendorCode."') and (CompCode eq '".$v_CompanyCode."'))");
//$api_request_url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_CURRENT_VENDOR_BAL_SRV/CreditorBalancesSet";
//$api_request_url .= "?".http_build_query($api_request_parameters);
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_CURRENT_VENDOR_BAL_SRV/CreditorBalancesSet?\$filter=%28%28Vendor%20eq%20%27".$v_VendorCode."%27%29%20and%20%28CompCode%20eq%20%27".$v_CompanyCode."%27%29%29";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Vendor ".$v_VendorCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
		$jsonoutput = json_decode($body,true);
		$numofrecords = sizeof($jsonoutput['d']['results']);
		$speech = "Total number of records ".$numofrecords;
		$speech .= "\r\n";
		$speech .= "Balance Information in all months for vender ".$v_VendorCode."\r\n";
		$speech .= "Vendor\tCompCode\tFiscYear\tFisPeriod\tDebitsMth\tCreditMth\tCurrency\tBalance\tMnthSales";
				
		for($x=0;$x<$numofrecords;$x++) 
		{
		   	$v_Vendor = $jsonoutput['d']['results'][$x]['Vendor'];
			$v_CompCode = $jsonoutput['d']['results'][$x]['CompCode'];
			$v_FiscYear = $jsonoutput['d']['results'][$x]['FiscYear'];
			$v_FisPeriod = $jsonoutput['d']['results'][$x]['FisPeriod'];
			$v_DebitsMth = $jsonoutput['d']['results'][$x]['DebitsMth'];
			$v_CreditMth = $jsonoutput['d']['results'][$x]['CreditMth'];
			$v_Currency = $jsonoutput['d']['results'][$x]['Currency'];
			$v_Balance = $jsonoutput['d']['results'][$x]['Balance'];
			$v_MnthSales = $jsonoutput['d']['results'][$x]['MnthSales'];
						
			$speech .= "\r\n";	
			$speech .= "$v_Vendor \t $v_CompCode \t $v_FiscYear \t $v_FisPeriod \t $v_DebitsMth \t $v_CreditMth \t $v_Currency \t $v_Balance \t $v_MnthSales";
			$speech .= "\r\n";	
		}
	}
		
	}
	
//-------------------------------------
//--vendor balance details ends here
//--------------------------------------
//------------------------------------
//--Vendor Current balance detail starts here
//-------------------------------------
if($json->queryResult->intent->displayName=='OPPcurrentVendorBal')
{
	if(isset($json->queryResult->parameters->CompCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
	if(isset($json->queryResult->parameters->VendorCode))
	{ 
		$v_VendorCode = $json->queryResult->parameters->VendorCode; 
	  	$v_VendorCode = strtoupper($v_VendorCode);
	}
	
	
	
$curl = curl_init();
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_CURRENT_VENDOR_BAL_SRV/CurrentVendorBalSet(Companycode='".$v_CompanyCode."',Vendor='".$v_VendorCode."')/?\$format=json";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Vendor ".$v_VendorCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
			$jsonoutput = json_decode($body);
		   	$v_Companycode = $jsonoutput->d->Companycode;
			$v_Vendor = $jsonoutput->d->Vendor;
			$v_CarryFwd = $jsonoutput->d->CarryFwd;
			$v_Currency = $jsonoutput->d->Currency;
			$v_Balance = $jsonoutput->d->Balance;
			$v_Crryfwdtot = $jsonoutput->d->Crryfwdtot;
			$v_Currency = $jsonoutput->d->Currency;
			$v_TotalBal = $jsonoutput->d->TotalBal;
			
			$speech = "\r\n";	
			$speech .= "Current Balance of vendor ".$v_Vendor." is ".$v_Currency." ".$v_Balance;
			
			$speech .= "\r\n";
			$speech .= "Other details are given below\r\n";
			$speech .= "Companycode\tVendor\tCarryFwd\tCurrency\tBalance\tCrryfwdtot\tCurrency\tTotalBal";			
			$speech .= "\r\n";
			$speech .= "$v_Companycode \t $v_Vendor \t $v_CarryFwd \t $v_Currency \t $v_Balance \t $v_Crryfwdtot \t $v_Currency \t $v_TotalBal";
			$speech .= "\r\n";	
		
	}
		
	}
	
//-------------------------------------
//--vendor current balance details ends here
//--------------------------------------
//--------------------------------------------------------------
//--Vendor Current balance detail on key date starts here
//-------------------------------------------------------------
if($json->queryResult->intent->displayName=='OPPcurrentVendorBalKeyDate')
{
	if(isset($json->queryResult->parameters->CompCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
	if(isset($json->queryResult->parameters->VendorCode))
	{ 
		$v_VendorCode = $json->queryResult->parameters->VendorCode; 
	  	$v_VendorCode = strtoupper($v_VendorCode);
	}
	if(isset($json->queryResult->parameters->KeyDate))
	{ 
		$v_KeyDate = $json->queryResult->parameters->KeyDate; 
	  	$v_KeyDate = strtoupper($v_KeyDate);
		$v_KeyDate = substr($v_KeyDate, 0, -6);
		//echo $v_KeyDate;
	}
	
	
	
$curl = curl_init();
															//2019-05-01T12:00:00+05:30
http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_CURRENT_VENDOR_BAL_SRV/KeyDateVendorBalSet(Vendor='1000120',Companycode='1710',Keydate=datetime'2019-04-25T00:00:00')/?$format=json

$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_CURRENT_VENDOR_BAL_SRV/KeyDateVendorBalSet(Vendor='".$v_VendorCode."',Companycode='".$v_CompanyCode."',Keydate=datetime'".$v_KeyDate."')/?\$format=json";
//echo $url;	
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Vendor ".$v_VendorCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
				$timestamp = strtotime($v_KeyDate);
				$new_date = date('d-F-Y', $timestamp);   
				$jsonoutput = json_decode($body);
				$speech = "\r\n";
				$v_Companycode = $jsonoutput->d->Companycode;
				//$v_Keydate = $jsonoutput->d->Keydate;
				$v_Vendor = $jsonoutput->d->Vendor;
				$v_Currency = $jsonoutput->d->Currency;
				$v_TCurrBal = $jsonoutput->d->TCurrBal;
				$speech .= "Current Balance on date ".$new_date." is ".$v_Currency." ".$v_TCurrBal." of Vendor ".$v_Vendor;
				$speech .= "\r\n";
				$speech .= "You can get more details from below link\r\n";
				$speech .= "https://tinyurl.com/FioriLinkDemo";
				$speech .= "\r\n";
		
	}
		
	}
	
//-------------------------------------
//--vendor current balance on key date details ends here
//--------------------------------------	

//--------------------------------------------------------------
//--Vendor address detail starts here
//-------------------------------------------------------------
if($json->queryResult->intent->displayName=='OPPcurrentVendorAddress')
{
	
	if(isset($json->queryResult->parameters->VendorCode))
	{ 
		$v_VendorCode = $json->queryResult->parameters->VendorCode; 
	  	$v_VendorCode = strtoupper($v_VendorCode);
	}
	
	
	
	
$curl = curl_init();
															
//http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/C_SupplierFs('1000120')/?$format=json


$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/C_SupplierFs('".$v_VendorCode."')/?\$format=json";
//echo $url;	
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Vendor ".$v_VendorCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
				$jsonoutput = json_decode($body);
				
				
				$v_AddressID = $jsonoutput->d->AddressID;
				
				$speech = "Address of the vendor ".$v_VendorCode." is :\r\n".$v_AddressID;
				$speech .= "\r\n";	
		
			}
		
	}
	
//-------------------------------------
//--vendor address details ends here
//--------------------------------------
//--------------------------------------------------------------
//--Vendor company address detail starts here
//-------------------------------------------------------------
if($json->queryResult->intent->displayName=='OPPcurrentVendorCompanyAddress')
{
	
	if(isset($json->queryResult->parameters->VendorCode))
	{ 
		$v_VendorCode = $json->queryResult->parameters->VendorCode; 
	  	$v_VendorCode = strtoupper($v_VendorCode);
	}
	if(isset($json->queryResult->parameters->CompCode))
	{ 
		$v_CompCode = $json->queryResult->parameters->CompCode; 
	  	$v_CompCode = strtoupper($v_CompCode);
	}
	
	
	
$curl = curl_init();
															
//http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/C_SupplierCompanyData(Supplier='1000120',CompanyCode='1710')/?$format=json

$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/C_SupplierCompanyData(Supplier='".$v_VendorCode."',CompanyCode='".$v_CompCode."')/?\$format=json";
//echo $url;	
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
//	echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Vendor ".$v_VendorCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
				$jsonoutput = json_decode($body);
				$v_CityName = $jsonoutput->d->CityName;
				$v_Country_Text = $jsonoutput->d->Country_Text;
				$v_Supplier = $jsonoutput->d->Supplier;
				$v_CompanyCode = $jsonoutput->d->CompanyCode;
				
				$speech = "Company address of vendor ".$v_Supplier." is :\r\n".$v_CityName.", ".$v_Country_Text."\r\n";
				$speech .= "\r\n";	
		
			}
		
	}
	
//-------------------------------------
//--vendor company address details ends here
//--------------------------------------

//--------------------------------------------------------------
//--Vendor Specific Industry  detail starts here
//-------------------------------------------------------------
if($json->queryResult->intent->displayName=='OPPSuppIndustryInfoSpecific')
{
	
	if(isset($json->queryResult->parameters->IndustryCode))
	{ 
		$v_IndustryCode = $json->queryResult->parameters->IndustryCode; 
	  	$v_IndustryCode = strtoupper($v_IndustryCode);
	}
	
	
	
	
$curl = curl_init();
															
//http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/C_SupplierFs('1000120')/?$format=json
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/I_SupplierIndustryText(Language='EN',SupplierIndustry='".$v_IndustryCode."')/?\$format=json";
//echo $url;	
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Industry ".$v_IndustryCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
				$jsonoutput = json_decode($body);
				
				
				$v_SupplierIndustry = $jsonoutput->d->SupplierIndustry;
				$v_SupplierIndustryName = $jsonoutput->d->SupplierIndustryName;
				
				$speech = "Supplier Industry Name of industry code ".$v_SupplierIndustry." is :\r\n".$v_SupplierIndustryName;
				$speech .= "\r\n";	
		
			}
		
	}
	
//-------------------------------------
//--vendor Specific Industry details ends here
//--------------------------------------	

//--------------------------------------------------------------
//--Vendor all Industry  detail starts here
//-------------------------------------------------------------
if($json->queryResult->intent->displayName=='OPPSuppIndustryInfoAll')
{
	
		
$curl = curl_init();
															
//http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/C_SupplierFs('1000120')/?$format=json
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/C_SUPPLIER_FS_SRV/I_SupplierIndustryText/?\$format=json";
//echo $url;	
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Accept:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "Industry ".$v_IndustryCode." does not exist";
				$speech .= "\r\n";
			}
			else 
			{
				$jsonoutput = json_decode($body,true);
				
				//--------------------------------------------------------
				$numofrecords = sizeof($jsonoutput['d']['results']);
				$speech = "Total number of records ".$numofrecords;
				$speech .= "\r\n";
				$speech .= "Supplier Industry Code"."  \t   "."Supplier Industry Name";

				for($x=0;$x<$numofrecords;$x++) 
				{
					$v_SupplierIndustry = $jsonoutput['d']['results'][$x]['SupplierIndustry'];
					$v_SupplierIndustryName = $jsonoutput['d']['results'][$x]['SupplierIndustryName'];

					$speech .= "\r\n";	
					$speech .= $v_SupplierIndustry."                    \t       ".$v_SupplierIndustryName;
					$speech .= "\r\n";	
				}
				
		
			}
		
	}
	
//-------------------------------------
//--vendor all Industry details ends here
//--------------------------------------

//----------------------------------------------------------------------------------	
	
	
	//sap integration -- open posting period
	if($json->queryResult->intent->displayName=='OPPacctype')
	{
		$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAC_GL_MAINT_POSTING_PERIOD_SRV/VL_FV_FAC_OPP_ACCOUNT_TYPE/?\$format=json",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY="
  ),
));

$response = curl_exec($curl);
		//echo $response;
$err = curl_error($curl);

curl_close($curl);
		$jsonoutput = json_decode($response,true);
		$numofusers = sizeof($jsonoutput['d']['results']);
		$speech = "Total number of Accounts ".$numofusers;
		$speech .= "\r\n";
		$speech .= "Account Code\tAccount Type\n";
				
		for($x=0;$x<$numofusers;$x++) {
		   $acc_code = $jsonoutput['d']['results'][$x]['Code'];
			$acc_type = $jsonoutput['d']['results'][$x]['Text'];
			
			$speech .=  $acc_code."\t".$acc_type;
			$speech .= "\r\n";	
			}
	}
	//display company code
if($json->queryResult->intent->displayName=='OPPCompanyCode')
{
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAC_GL_DOCUMENT_POST_SRV/VL_SH_H_T001/?\$format=json",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY="
  ),
));

$response = curl_exec($curl);
		//echo $response;
$err = curl_error($curl);

curl_close($curl);
		$jsonoutput = json_decode($response,true);
		$numofusers = sizeof($jsonoutput['d']['results']);
		$speech = "Total number of Companies ".$numofusers;
		$speech .= "\r\n";
		$speech .= "Company Code(BUKRS)\tCompany Name(BUTXT)\tLocation(ORT01)\tWAERS\n";
				
		for($x=0;$x<$numofusers;$x++) {
		   $comp_code = $jsonoutput['d']['results'][$x]['BUKRS'];
			$comp_name = $jsonoutput['d']['results'][$x]['BUTXT'];
			$comp_loc = $jsonoutput['d']['results'][$x]['ORT01'];
			$comp_cur = $jsonoutput['d']['results'][$x]['WAERS'];
			
			$speech .=  $comp_code."\t".$comp_name."\t".$comp_loc."\t".$comp_cur;
			$speech .= "\r\n";	
			}
	}
	
	//display single account for posting period data 
if($json->queryResult->intent->displayName=='OPPdataSingle')
{
	if(isset($json->queryResult->parameters->PostgPerdVar))
	{ 
		$v_PostgPerdVar = $json->queryResult->parameters->PostgPerdVar; 
	  	$v_PostgPerdVar= strtoupper($v_PostgPerdVar);
	}
	if(isset($json->queryResult->parameters->FiscalYearVar))
	{ 
		$v_FiscalYearVar = $json->queryResult->parameters->FiscalYearVar; 
	  	$v_FiscalYearVar= strtoupper($v_FiscalYearVar);
	}
	if(isset($json->queryResult->parameters->ToAcct))
	{ 
		$v_ToAcct = $json->queryResult->parameters->ToAcct; 
	  	$v_ToAcct= strtoupper($v_ToAcct);
	}
	if(isset($json->queryResult->parameters->AcctType))
	{ 
		$v_AcctType = $json->queryResult->parameters->AcctType; 
	  	$v_AcctType= strtoupper($v_AcctType);
	}
	
	$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAC_GL_MAINT_POSTING_PERIOD_SRV/PostingPeriodSet(PostgPerdVar='".$v_PostgPerdVar."',AcctType='".$v_AcctType."',ToAcct='".$v_ToAcct."',FiscalYearVar='".$v_FiscalYearVar."')/?"."\$format"."=json";
	//echo $url;
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_PORT => "8000",
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "",
	  CURLOPT_HTTPHEADER => array("Authorization: Basic YXJ1bm46Y3RsQDE5NzY="),
	));

	$response = curl_exec($curl);
			//echo $response;
	$err = curl_error($curl);

	curl_close($curl);
	$jsonoutput = json_decode($response);
	//$numofusers = sizeof($jsonoutput['d']);
	//$speech = "Total number of Compnies ".$numofusers;
	//$speech .= "\r\n";
	$speech = "Details are\n";

		$v_PostgPerdVar = $jsonoutput->d->PostgPerdVar;
		$v_AcctType = $jsonoutput->d->AcctType;
		$v_ToAcct = $jsonoutput->d->ToAcct;
		$v_FiscalYearVar = $jsonoutput->d->FiscalYearVar;
		$v_FromAcct = $jsonoutput->d->FromAcct;
		$v_PostgPerdVarDesc = $jsonoutput->d->PostgPerdVarDesc;
		$v_FiscalYearVarDesc = $jsonoutput->d->FiscalYearVarDesc;
		$v_AcctTypeDesc = $jsonoutput->d->AcctTypeDesc;
		$v_NumPostingPer = $jsonoutput->d->NumPostingPer;
		$v_NumSpePeriod = $jsonoutput->d->NumSpePeriod;
		$v_OpvPerdActionType = $jsonoutput->d->OpvPerdActionType;
		$v_OpvPerdYrStrt = $jsonoutput->d->OpvPerdYrStrt;
		$v_OpvPerdMnthStrt = $jsonoutput->d->OpvPerdMnthStrt;
		$v_OpvPerdYrEnd = $jsonoutput->d->OpvPerdYrEnd;
		$v_OpvPerdMnthEnd = $jsonoutput->d->OpvPerdMnthEnd;
		$v_AdjtPerdYrStrt = $jsonoutput->d->AdjtPerdYrStrt;
		$v_AdjtPerdMnthStrt = $jsonoutput->d->AdjtPerdMnthStrt;
		$v_AdjtPerdYrEnd = $jsonoutput->d->AdjtPerdYrEnd;
		$v_AdjtPerdMnthEnd = $jsonoutput->d->AdjtPerdMnthEnd;
		
		$speech .=  "PostgPerdVar"."\t".$v_PostgPerdVar."\n"."AcctType"."\t".$v_AcctType."\n";
		$speech .=  "ToAcct"."\t".$v_ToAcct."\n"."FiscalYearVar"."\t".$v_FiscalYearVar."\n";
		$speech .=  "FromAcct"."\t".$v_FromAcct."\n"."PostgPerdVarDesc"."\t".$v_PostgPerdVarDesc."\n";
		$speech .=  "FiscalYearVarDesc"."\t".$v_FiscalYearVarDesc."\n"."AcctTypeDesc"."\t".$v_AcctTypeDesc."\n";
		$speech .=  "NumPostingPer"."\t".$v_NumPostingPer."\n"."NumSpePeriod"."\t".$v_NumSpePeriod."\n";
		$speech .=  "OpvPerdActionType"."\t".$v_OpvPerdActionType."\n"."OpvPerdYrStrt"."\t".$v_OpvPerdYrStrt."\n";
		$speech .=  "OpvPerdMnthStrt"."\t".$v_OpvPerdMnthStrt."\n"."OpvPerdYrEnd"."\t".$v_OpvPerdYrEnd."\n";
		$speech .=  "OpvPerdMnthEnd"."\t".$v_OpvPerdMnthEnd."\n"."AdjtPerdYrStrt"."\t".$v_AdjtPerdYrStrt."\n";
		$speech .=  "AdjtPerdMnthStrt"."\t".$v_AdjtPerdMnthStrt."\n"."AdjtPerdYrEnd"."\t".$v_AdjtPerdYrEnd."\n";
		$speech .=  "AdjtPerdMnthEnd"."\t".$v_AdjtPerdMnthEnd."\n";
		$speech .= "\r\n";	
	
}

//---------------------------------------------------
//----CUSTOM ODATA SERVICES STARTS HERE-------------
//---------------------------------------------------

//---------------------------------------------------
//--OPP DISPLAY ALL RECORDS STARTS HERE
//---------------------------------------------------
if($json->queryResult->intent->displayName=='OPPCustomDisAll')
{
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet/?\$format=json",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY="
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);

		curl_close($curl);
		$jsonoutput = json_decode($response,true);
		$numofusers = sizeof($jsonoutput['d']['results']);
		$speech = "Total number of records ".$numofusers;
		$speech .= "\r\n";
		$speech .= "BUKRS\tMandt\tMkoar\tBkont\tFromYear1\tFromPer1\tToYear1\tToPer1\n";
				
		for($x=0;$x<$numofusers;$x++) 
		{
		   	$v_BUKRS = $jsonoutput['d']['results'][$x]['Bukrs'];
			$v_Mandt = $jsonoutput['d']['results'][$x]['Mandt'];
			$v_Mkoar = $jsonoutput['d']['results'][$x]['Mkoar'];
			$v_Bkont = $jsonoutput['d']['results'][$x]['Bkont'];
			$v_Frye1 = $jsonoutput['d']['results'][$x]['Frye1'];
			$v_Frpe1 = $jsonoutput['d']['results'][$x]['Frpe1'];
			$v_Toye1 = $jsonoutput['d']['results'][$x]['Toye1'];
			$v_Tope1 = $jsonoutput['d']['results'][$x]['Tope1'];
			
			
			$speech .=  $v_BUKRS."\t".$v_Mandt."\t".$v_Mkoar."\t\t".$v_Bkont."\t\t".$v_Frye1."\t\t".$v_Frpe1."\t\t".$v_Toye1."\t\t".$v_Tope1;
			$speech .= "\r\n";	
		}
	}
//---------------------------------------------------
//--OPP DISPLAY ALL RECORDS ENDS HERE
//---------------------------------------------------

//---------------------------------------------------
//--OPP DISPLAY SPECIFIC RECORD STARTS HERE
//---------------------------------------------------
if($json->queryResult->intent->displayName=='OPPCustomDisSpecific')
{
	
	if(isset($json->queryResult->parameters->CompanyCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompanyCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
		$allacctype = array("+","A","D","K","M","S","V");
		$json = json_decode($requestBody,true);
		$numofaccts = sizeof($json['queryResult']['parameters']['AcctType']);
		if($numofaccts == 1 && strtoupper($json['queryResult']['parameters']['AcctType'][0]) == 'ALL')
		{
			for($x=0;$x<count($allacctype);$x++)
			{
				$array_AcctType[$x] = $allacctype[$x];
				
				//echo $array_AcctType[$x];
			}
			
		}
		else
		{
			for($x=0;$x<$numofaccts;$x++) 
			{
				$array_AcctType[] = strtoupper($json['queryResult']['parameters']['AcctType'][$x]);
			}
		}
	
		//$speech = "Total number of accounts ".$numofaccts."\r\n";
		
		
	$numofaccts = count($array_AcctType);
$speech = "Account Type \t Account No \t From Year\t From Period\t To Year\t To Period\n";
	for($x=0;$x<$numofaccts;$x++) 
	{
		
			  
			$v_AcctType = $array_AcctType[$x];
		  	$v_AcctType= strtoupper($v_AcctType);
	
	
$curl = curl_init();
//"http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAC_GL_MAINT_POSTING_PERIOD_SRV/PostingPeriodSet(PostgPerdVar='".$v_PostgPerdVar."',AcctType='".$v_AcctType."',ToAcct='".$v_ToAcct."',FiscalYearVar='".$v_FiscalYearVar."')/?"."\$format"."=json";	
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json";
//echo $url;
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_HEADER => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY="
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
	
	//--
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
 //--
	$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	
//---
curl_close($curl);
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="404NotFound")
	{
		$speech .= "No Posting period available for account type ".$v_AcctType." and "."posting period varient ".$v_CompanyCode;
		$speech .= "\r\n";
	}
	else 
	{
	

		
		$jsonoutput = json_decode($body);
		
				
		
		   	$v_Mkoar = $jsonoutput->d->Mkoar;
			$v_Bkont = $jsonoutput->d->Bkont;
			$v_Frye1 = $jsonoutput->d->Frye1;
			$v_Frpe1 = $jsonoutput->d->Frpe1;
			$v_Toye1 = $jsonoutput->d->Toye1;
			$v_Tope1 = $jsonoutput->d->Tope1;
			
			
			
			$speech .= $v_Mkoar."                   ".$v_Bkont."         ".$v_Frye1."               ".$v_Frpe1."         ".$v_Toye1."     ".$v_Tope1;
			$speech .= "\n";	
	}
}
}

//---------------------------------------------------
//--OPP DISPLAY SPECIFIC RECORD ENDS HERE
//---------------------------------------------------
	
//---------------------------------------------------
//--OPP UPDATE SPECIFIC RECORD STARTS HERE
//---------------------------------------------------
if($json->queryResult->intent->displayName=='OPPCustomUpdateSpecific')
{
	if(isset($json->queryResult->parameters->CompanyCode))
		{	$v_CompanyCode = $json->queryResult->parameters->CompanyCode; 
			$v_CompanyCode= strtoupper($v_CompanyCode);
		}
	if(isset($json->queryResult->parameters->AcctType))
		{	$v_AcctType = $json->queryResult->parameters->AcctType; 
			$v_AcctType= strtoupper($v_AcctType);
		}

$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
 CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
   // "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
//-------------------------------------------------------------------

// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$httpstatus = $headers[0];
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="404NotFound")
	{
		$speech .= "Record Doesn't exist in system";
		$speech .= "\r\n";
	}
//------------------------------------------------------------------	
else
{
			$jsonoutput = json_decode($body);
			$v_BUKRS = $jsonoutput->d->Bukrs;
			$v_Mandt = $jsonoutput->d->Mandt;
			$v_Mkoar = $jsonoutput->d->Mkoar;
			$v_Tope1 = $jsonoutput->d->Tope1;
	
	$speech = "Current value is:\r\n";
	
	$speech .= "Account Type\tTo Period\n";
	$speech .=  "  ".$v_Mkoar."                    ".$v_Tope1;
	$speech .= "\r\nDo you want to update To Period value (Yes/No) \n";
}
}

//--------YES UPDATE STATRS HERE-------------------
if($json->queryResult->intent->displayName=='OPPCustomUpdateSpecific-yes')
{
	if(isset($json->queryResult->parameters->CompanyCode))
		{	$v_CompanyCode = $json->queryResult->parameters->CompanyCode; 
			$v_CompanyCode= strtoupper($v_CompanyCode);
		}
	if(isset($json->queryResult->parameters->AcctType))
		{	$v_AcctType = $json->queryResult->parameters->AcctType; 
			$v_AcctType= strtoupper($v_AcctType);
		}
	if(isset($json->queryResult->parameters->ToPer))
		{	$v_ToPer = $json->queryResult->parameters->ToPer; 
			$v_ToPer= strtoupper($v_ToPer);
		}
	

$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);

//---------
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);
$jsonoutput = json_decode($body);
//echo $body;	
	
			//Fetching all values to create payload to update request
			$v_BUKRS = $jsonoutput->d->Bukrs;
			$v_Mandt = $jsonoutput->d->Mandt;
			$v_Mkoar = $jsonoutput->d->Mkoar;
			$v_Rrcty = $jsonoutput->d->Rrcty;
			$v_Bkont = $jsonoutput->d->Bkont;
			$v_Frye2 = $jsonoutput->d->Frye2;
			$v_Vkont = $jsonoutput->d->Vkont;
			$v_Frpe2 = $jsonoutput->d->Frpe2;
			$v_Frye1 = $jsonoutput->d->Frye1;
			$v_Toye2 = $jsonoutput->d->Toye2;
			$v_Frpe1 = $jsonoutput->d->Frpe1;
			$v_Tope2 = $jsonoutput->d->Tope2;
			$v_Toye1 = $jsonoutput->d->Toye1;
			$v_Brgru = $jsonoutput->d->Brgru;
			$v_Tope1 = $jsonoutput->d->Tope1;
			$v_Frye3 = $jsonoutput->d->Frye3;
			$v_Frpe3 = $jsonoutput->d->Frpe3;
			$v_Toye3 = $jsonoutput->d->Toye3;
			$v_Tope3 = $jsonoutput->d->Tope3;

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$token = $headers[5];
$sapcookie = $headers[2];
preg_match("/SAP_SESSIONID_SMF_100(.*?)\;/", $sapcookie, $matches);
$token = substr($token,14);
//$speech = "Token fetched ".$token;



//put request
$jsonvar = array(
				'Bukrs'=> $v_BUKRS,
				'Mandt'=> $v_Mandt,
				'Mkoar'=> $v_Mkoar,
				'Rrcty'=> $v_Rrcty,
				'Bkont'=> $v_Bkont,
				'Frye2'=> $v_Frye2,
				'Vkont'=> $v_Vkont,
				'Frpe2'=> $v_Frpe2,
				'Frye1'=> $v_Frye1,
				'Toye2'=> $v_Toye2,
				'Frpe1'=> $v_Frpe1,
				'Tope2'=> $v_Tope2,
				'Toye1'=> $v_Toye1,
				'Brgru'=> $v_Brgru,
				'Tope1'=> $v_ToPer,
				'Frye3'=> $v_Frye3,
				'Frpe3'=> $v_Frpe3,
				'Toye3'=> $v_Toye3,
				'Tope3'=> $v_Tope3

			
		);
$jsonvar = json_encode($jsonvar);
	//echo $jsonvar;
$curl = curl_init();
$csrftoken = "x-CSRF-Token:".$token; // Prepare the csrf token
$v_cookie =  "SAP_SESSIONID_SMF_100".$matches[1]; //Prepare cookie value sap session id
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_COOKIE => $v_cookie,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $jsonvar,
  CURLOPT_HTTPHEADER => array(
	  "Content-Type: application/json",
	  "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  $csrftoken),
));

$response = curl_exec($curl);
$err = curl_error($curl);
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	
//---
	
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="204NoContent")
	{
		$speech .= "Record Updated Successfully";
		$speech .= "\r\n";
	}
	else 
	{
		$speech = $err;
	}	
//---
	
//GET REQUEST EXECUTING AGAIN TO CHECK FLAG STATUS
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  //CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
$jsonoutput = json_decode($response);

//echo $response;	

			$v_BUKRS = $jsonoutput->d->Bukrs;
			$v_Mandt = $jsonoutput->d->Mandt;
			$v_Mkoar = $jsonoutput->d->Mkoar;
			$v_Tope1 = $jsonoutput->d->Tope1;
	
	$speech .= "Updated value is : \r\n";
	
	$speech .= "AccountType\tTo Period\n";
	$speech .=  "  ".$v_Mkoar."                    ".$v_Tope1;
//-----GET REQUEST AGAIN ENDS
}

//--------YES UPDATE ENDS HERE---------------------
	
//---------------------------------------------------
//--OPP UPDATE SPECIFIC RECORD ENDS HERE
//---------------------------------------------------

//---------------------------------------------------
//--OPP CREATE NEW RECORD STARTS HERE OPPCustomCreateNew
//---------------------------------------------------
if($json->queryResult->intent->displayName=='OPPCustomCreateNew-custom')
	
{
	
		if(isset($json->queryResult->parameters->CompanyCode))
		{	$v_CompanyCode = $json->queryResult->parameters->CompanyCode; 
			$v_CompanyCode= strtoupper($v_CompanyCode);
		}
		
		if(isset($json->queryResult->parameters->ToPer))
		{	$v_ToPer = $json->queryResult->parameters->ToPer; 
			$v_ToPer= strtoupper($v_ToPer);
		}
	//-----------------------------------------------------------------------------------------
	$allacctype = array("+","A","D","K","M","S","V");
		$json = json_decode($requestBody,true);
		$numofaccts = sizeof($json['queryResult']['parameters']['AcctType']);
		if($numofaccts == 1 && strtoupper($json['queryResult']['parameters']['AcctType'][0]) == 'ALL')
		{
			for($x=0;$x<count($allacctype);$x++)
			{
				$array_AcctType[$x] = $allacctype[$x];
				
				//echo $array_AcctType[$x];
			}
			
		}
		else
		{
			for($x=0;$x<$numofaccts;$x++) 
			{
				$array_AcctType[] = strtoupper($json['queryResult']['parameters']['AcctType'][$x]);
			}
		}
	
		//$speech = "Total number of accounts ".$numofaccts."\r\n";
		
		
	$numofaccts = count($array_AcctType);
	
	
	//-----------------------------------------------------------------------------------------
	
		
$speech = "Total number of accounts ".$numofaccts."\r\n";
		
for($x=0;$x<$numofaccts;$x++) 
{
	$v_AcctType = $array_AcctType[$x]; 

//----------------------------------------------------------------------------
$curl = curl_init();
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet/?\$format=json";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);

//---------
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$token = $headers[5];
$sapcookie = $headers[2];
preg_match("/SAP_SESSIONID_SMF_100(.*?)\;/", $sapcookie, $matches);
$token = substr($token,14);

//put request
$jsonvar = array(
				'Bukrs'=> $v_CompanyCode,
				'Mandt'=> '100',
				'Mkoar'=> $v_AcctType,
				'Rrcty'=> '0',
				'Bkont'=> 'ZZZZZZZZZZ',
				'Frye2'=> '0000',
				'Vkont'=> '',
				'Frpe2'=> '000',
				'Frye1'=> '2018',
				'Toye2'=> '0000',
				'Frpe1'=> '000',
				'Tope2'=> '000',
				'Toye1'=> '2019',
				'Brgru'=> '',
				'Tope1'=> $v_ToPer,
				'Frye3'=> '0000',
				'Frpe3'=> '000',
				'Toye3'=> '0000',
				'Tope3'=> '000'

			
		);
$jsonvar = json_encode($jsonvar);
	//echo $jsonvar;
$curl = curl_init();
$csrftoken = "x-CSRF-Token:".$token; // Prepare the csrf token
$v_cookie =  "SAP_SESSIONID_SMF_100".$matches[1]; //Prepare cookie value sap session id
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_COOKIE => $v_cookie,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $jsonvar,
  CURLOPT_HTTPHEADER => array(
	  "Content-Type: application/json",
	  "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  $csrftoken),
));

$response = curl_exec($curl);
$err = curl_error($curl);
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	
//---
	
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="201Created")
	{
		$speech .= "Opened Posting Period for Account type ".$v_AcctType;
		$speech .= "\r\n";
		
	}
	else 
	{
		$speech .= $err;
	}	
	
}//account loop ends here
//------------------------------------
//--display newly created records
//---------------------------------------
$speech .= "Details are given below\r\n";
$speech1 = "Account Type \t Account No \t From Year\t From Period\t To Year\t To Period\r\n";

for($x=0;$x<$numofaccts;$x++) 
{
		   $v_AcctType = $array_AcctType[$x];
	$curl = curl_init();
//"http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAC_GL_MAINT_POSTING_PERIOD_SRV/PostingPeriodSet(PostgPerdVar='".$v_PostgPerdVar."',AcctType='".$v_AcctType."',ToAcct='".$v_ToAcct."',FiscalYearVar='".$v_FiscalYearVar."')/?"."\$format"."=json";	
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json";
//echo $url;
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_HEADER => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY="
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
	
	//--
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
 //--
	$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	
//---
curl_close($curl);
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="404NotFound")
	{
		$speech .= "Record Doesn't exist in system";
		$speech .= "\r\n";
	}
	else 
	{
	

		
		$jsonoutput = json_decode($body);
		//$speech .= "BUKRS\tMandt\tMkoar\tBkont\tFromYear1\tFromPer1\tToYear1\tToPer1\n";
				
		
		   	$v_BUKRS = $jsonoutput->d->Bukrs;
			$v_Mandt = $jsonoutput->d->Mandt;
			$v_Mkoar = $jsonoutput->d->Mkoar;
			$v_Bkont = $jsonoutput->d->Bkont;
			$v_Frye1 = $jsonoutput->d->Frye1;
			$v_Frpe1 = $jsonoutput->d->Frpe1;
			$v_Toye1 = $jsonoutput->d->Toye1;
			$v_Tope1 = $jsonoutput->d->Tope1;
			
			
			$speech1 .= $v_Mkoar."                   ".$v_Bkont."         ".$v_Frye1."               ".$v_Frpe1."         ".$v_Toye1."     ".$v_Tope1;
			//$speech .=  $v_BUKRS."\t".$v_Mandt."\t".$v_Mkoar."\t\t".$v_Bkont."\t\t".$v_Frye1."\t\t".$v_Frpe1."\t\t".$v_Toye1."\t\t".$v_Tope1;
			$speech1 .= "\r\n";	
	}
}
//---ends display newly created records
$speech .= $speech1;
	
}
	
//---------------------------------------------------
//--OPP CREATE NEW RECORD ENDS HERE
//---------------------------------------------------

//---------------------------------------------------
//--OPP DELETE RECORD STARTS HERE OPPCustomDelSpecific
//---------------------------------------------------
if($json->queryResult->intent->displayName=='OPPCustomDelSpecific')
{
	
	if(isset($json->queryResult->parameters->CompanyCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompanyCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
	$json = json_decode($requestBody,true);
		$numofaccts = sizeof($json['queryResult']['parameters']['AcctType']);
		$speech = "Total number of accounts ".$numofaccts."\r\n";
		
for($x=0;$x<$numofaccts;$x++) 
{
		   $v_AcctType = $json['queryResult']['parameters']['AcctType'][$x];
		   $v_AcctType= strtoupper($v_AcctType);
	/*if(isset($json->queryResult->parameters->AcctType))
	{ 
		$v_AcctType = $json->queryResult->parameters->AcctType; 
	  	$v_AcctType= strtoupper($v_AcctType);
	}*/
$curl = curl_init();
//"http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAC_GL_MAINT_POSTING_PERIOD_SRV/PostingPeriodSet(PostgPerdVar='".$v_PostgPerdVar."',AcctType='".$v_AcctType."',ToAcct='".$v_ToAcct."',FiscalYearVar='".$v_FiscalYearVar."')/?"."\$format"."=json";	
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json";
//echo $url;
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_HEADER => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	   "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);

//---------
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$token = $headers[5];
$sapcookie = $headers[2];
preg_match("/SAP_SESSIONID_SMF_100(.*?)\;/", $sapcookie, $matches);
$token = substr($token,14);
$httpstatus = $headers[0];
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="404NotFound")
	{
		$speech .= "Record Doesn't exist in system for ".$v_AcctType;
		$speech .= "\r\n";
	}
	else 
	{
		$curl = curl_init();
		$csrftoken = "x-CSRF-Token:".$token; // Prepare the csrf token
		$v_cookie =  "SAP_SESSIONID_SMF_100".$matches[1]; //Prepare cookie value sap session id
		$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')";
		curl_setopt_array($curl, array(
		  CURLOPT_PORT => "8000",
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_HEADER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_COOKIE => $v_cookie,
		  //CURLOPT_POST => true,
		  //CURLOPT_POSTFIELDS => $jsonvar,
		  CURLOPT_HTTPHEADER => array(
			  "Content-Type: application/json",
			  "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
			  $csrftoken),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		//echo $headers;
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
			if($v_res=="204NoContent")
			{
				$speech .= "Record deleted Successfully for ".$v_AcctType;
				$speech .= "\r\n";
			}
			else 
			{
				$speech = $err;
			}	
		
		
	}
}//for loop ends
}

	
//---------------------------------------------------
//--OPP DELETE RECORD ENDS HERE OPPCustomDelSpecific
//---------------------------------------------------
	
	
//------------------------------------------	
//VENDOR/SUPPLIER SCENARIOS STARTS HERE
//------------------------------------------
	//------------------------------------------
	//--SUPPLIER BALANCE STARTS HERE
	//------------------------------------------
if($json->queryResult->intent->displayName=='OPPSupBalSet')
{
	if(isset($json->queryResult->parameters->CompCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
	if(isset($json->queryResult->parameters->SuppCode))
	{ 
		$v_SuppCode = $json->queryResult->parameters->SuppCode; 
	  	$v_SuppCode= strtoupper($v_SuppCode);
	}
	if(isset($json->queryResult->parameters->FiscalYear))
	{ 
		$v_FiscalYear = $json->queryResult->parameters->FiscalYear; 
	  	$v_FiscalYear= strtoupper($v_FiscalYear);
	}
	
	
$curl = curl_init();

$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/FAP_VENDOR_BALANCE_SRV/SupplierBalanceSet?\$filter=%28%28Supplier%20eq%20%27".$v_SuppCode."%27%29%20and%20%28CompanyCode%20eq%20%27".$v_CompanyCode."%27%29%29%20and%20%28FiscalYear%20eq%20%27".$v_FiscalYear."%27%29&\$format=json";
	//echo $url;
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  "Content-Type:application/json"
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);
		// Return headers seperatly from the Response Body
		  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $headers = substr($response, 0, $header_size);
		  $body = substr($response, $header_size);
		  header("Content-Type:application/json");
		  curl_close($curl);

		$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
		$headers = array_filter($headers);
		//extracting status from header
		$httpstatus = $headers[0];

		//---

		preg_match("/HTTP\/1.0(.*)/", $httpstatus, $res);
		//echo $res[1];
			$v_res = str_replace(' ', '', $res[1]);
	//echo $v_res;
	//echo $body;
			if($v_res=="400BadRequest" )
			{
				$speech = "No data present for given fiscal year";
				$speech .= "\r\n";
			}
			else 
			{
				$jsonoutput = json_decode($body,true);
		$numofrecords = sizeof($jsonoutput['d']['results']);
		//$speech = "Total number of records ".$numofusers;
		//$speech .= "\r\n";
		//$speech .= "BUKRS\tMandt\tMkoar\tBkont\tFromYear1\tFromPer1\tToYear1\tToPer1\n";
				
		//for($x=0;$x<$numofusers;$x++) 
		//{
		   	$v_BalAmtInDisplayCrcy = $jsonoutput['d']['results'][$numofrecords-1]['BalAmtInDisplayCrcy'];
			$v_Currency = $jsonoutput['d']['results'][$numofrecords-1]['Currency'];
			$speech .=  "Here is the cumulative balance for supplier #  ".$v_SuppCode.":\n".$v_Currency." ".$v_BalAmtInDisplayCrcy;
			$speech .= "\r\n";	
			$speech .= "Do you wish to see line item details?";
		//}
			}	
		
	}
	//------------------------------------------
	//--SUPPLIER BALANCE ENDS HERE
	//------------------------------------------
//--vendor/supplier more detail on fiori
	
	
if($json->queryResult->intent->displayName=='OPPSupBalSet - yes')
{
	if(isset($json->queryResult->parameters->CompCode))
	{ 
		$v_CompanyCode = $json->queryResult->parameters->CompCode; 
	  	$v_CompanyCode= strtoupper($v_CompanyCode);
	}
	if(isset($json->queryResult->parameters->SuppCode))
	{ 
		$v_SuppCode = $json->queryResult->parameters->SuppCode; 
	  	$v_SuppCode= strtoupper($v_SuppCode);
	}
	if(isset($json->queryResult->parameters->FiscalYear))
	{ 
		$v_FiscalYear = $json->queryResult->parameters->FiscalYear; 
	  	$v_FiscalYear= strtoupper($v_FiscalYear);
	}
	
	
	//<html><a href="http://sealapp2.sealconsult.com:8000/sap/bc/ui5_ui5/ui2/ushell/shells/abap/Fiorilaunchpad.html#Supplier-manageLineItems">Click here</a></html>;
	
	$uri = "https://tinyurl.com/FioriLinkDemo";		
	$speech = "Let me redirect you to SAP Fiori\r\nPlease click on below link to get details.\r\n";
	$speech .= $uri;
	
}
	
//--vendor/supplier more detail on fiori end

	
	


//------------------------------------------	
//VENDOR/SUPPLIER SCENARIOS ENDS HERE
//------------------------------------------		
	
//---------------------------------------------------
//----CUSTOM ODATA SERVICES ENDS HERE-------------
//---------------------------------------------------


	
	//sap integration -- open posting period ends here
	
	//--sap integration
	
	if($json->queryResult->intent->displayName=='SAPUserList')
	{
		$curl = curl_init();
		 // Prepare the authorisation token
		curl_setopt_array($curl, array(
		  CURLOPT_PORT => "8000",
		  CURLOPT_URL => "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UsersListSet/?\$format=json",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		 //CURLOPT_USERPWD=> "$username:$password",
		  CURLOPT_HTTPHEADER => array(
		   "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
		   "Content-Type: application/json" ),	));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		$jsonoutput = json_decode($response,true);
		$numofusers = sizeof($jsonoutput['d']['results']);
		$speech = "Total number of Users ".$numofusers;
		$speech .= "\r\n";
		$speech .= "UserName\tFirstName\tLastName\n";
				//$gg = sizeof($jsonoutput['d']['results']);
		for($x=0;$x<$numofusers;$x++) {
		   $username = $jsonoutput['d']['results'][$x]['Username'];
			$firstname = $jsonoutput['d']['results'][$x]['Firstname'];
			$lastname = $jsonoutput['d']['results'][$x]['Lastname'];

			$speech .=  $username."\t".$firstname ."\t".$lastname;
						$speech .= "\r\n";	
			}
	}

//get detial of sap user
if($json->queryResult->intent->displayName=='SAPGivenUser')
{
	if(isset($json->queryResult->parameters->username))
		{ $username = $json->queryResult->parameters->username; 
			$username= strtoupper($username);
		}
		
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserDetailsSet%28%27".$username."%27%29/?\$format=json";
//	"http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserDetailsSet('".$username."')/?\$format=json";	
	
$curl = curl_init();
	
 // Prepare the authorisation token
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
 //CURLOPT_USERPWD=> "$username:$password",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "Content-Type: application/json"
   
  ),
	
 
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
	$jsonoutput = json_decode($response);
	$username = 	$jsonoutput->d->Username;
	$Company = 	$jsonoutput->d->Company;
	$PersNo=	$jsonoutput->d->PersNo;
	$Firstname = 	$jsonoutput->d->Firstname;
	$Lastname = 	$jsonoutput->d->Lastname;
	$Fullname=	$jsonoutput->d->Fullname;
	$City = 	$jsonoutput->d->City;
	$District = 	$jsonoutput->d->District;
	$PoBox=		$jsonoutput->d->PoBox;
	$Street = 	$jsonoutput->d->Street;
	$Location = 	$jsonoutput->d->Location;
	$Langu=		$jsonoutput->d->Langu;
	$Region=	$jsonoutput->d->Region;
	$Tel1Numbr=	$jsonoutput->d->Tel1Numbr;
	$LocalLock=	$jsonoutput->d->LocalLock;
	
	$speech = "Username = ".$username."\n"."Company = ".$Company."\n"."Personal num = ".$PersNo."\n"."Fullname = ".$Fullname."\n"."Lock Status = ".$LocalLock;
	//$speech = $username;
}


//-----------------------------
//SAP ACCOUNT UNLOCK BEGINS HERE
//------------------------------
if($json->queryResult->intent->displayName=='SAPUnlockaccount')
{
	if(isset($json->queryResult->parameters->username))
		{	$username = $json->queryResult->parameters->username; 
			$username= strtoupper($username);
		}
//http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserUnLockSet('CHATBOT1')/?$format=json
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserUnLockSet('".$username."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
//echo $response;


//---------
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);
$jsonoutput = json_decode($body);

//echo $body;	
$lockstatus = $jsonoutput->d->IsLockedFlag;
if ($lockstatus == "U")
{
		$speech .= "This account is UNLOCKED already.";
		$speech .= "\r\n";
}	
else
{

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$token = $headers[5];
$sapcookie = $headers[2];
preg_match("/SAP_SESSIONID_SMF_100(.*?)\;/", $sapcookie, $matches);
$token = substr($token,14);
//$speech = "Token fetched ".$token;

//put request
$jsonvar = array(
			'Username'=> $username,
			
		);
$jsonvar = json_encode($jsonvar);
	//echo $jsonvar;
$curl = curl_init();
$csrftoken = "x-CSRF-Token:".$token; // Prepare the csrf token
$v_cookie =  "SAP_SESSIONID_SMF_100".$matches[1]; //Prepare cookie value sap session id
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserUnLockSet('".$username."')/";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_COOKIE => $v_cookie,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $jsonvar,
  CURLOPT_HTTPHEADER => array(
	  "Content-Type: application/json",
	  "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  $csrftoken),
));

$response = curl_exec($curl);
$err = curl_error($curl);
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	
//GET REQUEST EXECUTING AGAIN TO CHECK FLAG STATUS
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserLockSet('".$username."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  //CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
$jsonoutput = json_decode($response);

//echo $response;	
$lockstatus = $jsonoutput->d->IsLockedFlag;
if ($lockstatus == "U")
{
		$speech .= "Account UNLOCKED Successfully.";
		$speech .= "\r\n";
}
//-----GET REQUEST AGAIN ENDS
	

}	
	
		
}

//-----------------------------
//SAP ACCOUNT UNLOCK ENDS HERE
//------------------------------	
	
//---------------------------------------
//CHANGE PASSWORD OF SAP USER BEGINS HERE
//----------------------------------------
if($json->queryResult->intent->displayName=='SAPchangePswd')
{
	if(isset($json->queryResult->parameters->sapusername))
		{	$username = $json->queryResult->parameters->sapusername; 
			$username= strtoupper($username);
		}
	if(isset($json->queryResult->parameters->newpswd))
		{ 	$newpswd = $json->queryResult->parameters->newpswd; 
			//$username= strtoupper($username);
		}
		
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserPwdChangeSet('".$username."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
//echo $response;
//---------
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$token = $headers[5];
$sapcookie = $headers[2];
preg_match("/SAP_SESSIONID_SMF_100(.*?)\;/", $sapcookie, $matches);
$token = substr($token,14);
//$speech = "Token fetched ".$token;

//put request
$jsonvar = array(
			'Username'=> $username,
			'Bapipwdx'=> 'X',
			'Bapipwd'=> $newpswd
		);
$jsonvar = json_encode($jsonvar);
	//echo $jsonvar;
$curl = curl_init();
$csrftoken = "x-CSRF-Token:".$token; // Prepare the csrf token
$v_cookie =  "SAP_SESSIONID_SMF_100".$matches[1]; //Prepare cookie value sap session id
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserPwdChangeSet('".$username."')/";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_COOKIE => $v_cookie,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $jsonvar,
  CURLOPT_HTTPHEADER => array(
	  "Content-Type: application/json",
	  "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  $csrftoken),
));

$response = curl_exec($curl);
$err = curl_error($curl);
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	//echo $httpstatus;
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="204NoContent")
	{
		$speech .= "Your password has changed successfully to default.";
		$speech .= " We will notify you through mail. Please change this password in SAP system at first login.";
		$speech .= "\r\n";
	}
	else 
	{
		$speech = $err;
	}
	
	
	
		
}
//Change password sap user account ends here
	
//----------------------------------
//Lock sap user account begin
//---------------------------

if($json->queryResult->intent->displayName=='SAPLockaccount')
{
	if(isset($json->queryResult->parameters->username))
		{	$username = $json->queryResult->parameters->username; 
			$username= strtoupper($username);
		}
	//http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserLockSet('CHATBOT1')/?$format=json
		
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserLockSet('".$username."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
//echo $response;


//---------
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
 //echo $headers;
  curl_close($curl);
$jsonoutput = json_decode($body);

//echo $body;	
$lockstatus = $jsonoutput->d->IsLockedFlag;
if ($lockstatus == "L")
{
		$speech .= "This account is locked already.";
		$speech .= "\r\n";
}	
else
{

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
$token = $headers[5];
$sapcookie = $headers[2];
preg_match("/SAP_SESSIONID_SMF_100(.*?)\;/", $sapcookie, $matches);
$token = substr($token,14);
//$speech = "Token fetched ".$token;

//put request
$jsonvar = array(
			'Username'=> $username,
			
		);
$jsonvar = json_encode($jsonvar);
	//echo $jsonvar;
$curl = curl_init();
$csrftoken = "x-CSRF-Token:".$token; // Prepare the csrf token
$v_cookie =  "SAP_SESSIONID_SMF_100".$matches[1]; //Prepare cookie value sap session id
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserLockSet('".$username."')/";
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_COOKIE => $v_cookie,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $jsonvar,
  CURLOPT_HTTPHEADER => array(
	  "Content-Type: application/json",
	  "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
	  $csrftoken),
));

$response = curl_exec($curl);
$err = curl_error($curl);
// Return headers seperatly from the Response Body
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  $headers = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  header("Content-Type:application/json");
  curl_close($curl);

$headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
$headers = array_filter($headers);
//extracting status from header
$httpstatus = $headers[0];
	
//GET REQUEST EXECUTING AGAIN TO CHECK FLAG STATUS
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/SAP/ZUSER_MAINT_OPRS_DEMO_SRV/UserLockSet('".$username."')/?"."\$format"."=json";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8000",
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  //CURLOPT_HEADER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YXJ1bm46Y3RsQDE5NzY=",
    "x-CSRF-Token: Fetch"
  ),
));

// Get the response body as string
$response = curl_exec($curl);
$jsonoutput = json_decode($response);

//echo $response;	
$lockstatus = $jsonoutput->d->IsLockedFlag;
if ($lockstatus == "L")
{
		$speech .= "Account Locked Successfully.";
		$speech .= "\r\n";
}
//-----GET REQUEST AGAIN ENDS
	

}	
	
		
}
//------------------------------	
//Lock sap user account ends here
//-------------------------------

	

	
	//SAP ends here


		//----SNOW IMPLEMENTATION----
  	if($json->queryResult->intent->displayName=='Raise_ticket_intent - GetnameGetissue')
	{
		//if(isset($json->queryResult->queryText))
		//{ $sh_desc = $json->queryResult->queryText; }

		if(isset($json->queryResult->parameters->name))
		{ $name = $json->queryResult->parameters->name; }
		
		if(isset($json->queryResult->parameters->issue))
		{ $sh_desc = $json->queryResult->parameters->issue; }

		$sh_desc = strtolower($sh_desc);
		//$sh_desc = "Testing";
		//$name = "someone";
		$instance = "dev75823";
		$username = "admin";
		$password = "Ctli1234";
		$table = "incident";
		
		$jsonobj = array('short_description' => $sh_desc);
             	$jsonobj = json_encode($jsonobj);	

		
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=insert";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonobj);
		}
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$incident_no =  $jsonoutput->records[0]->number;
		$sys_id = $jsonoutput->records[0]->sys_id;
		$speech = "Thanks ".$name."! Incident Created Successfully for issue " . $sh_desc . " and your incident number is " . $incident_no;
		$speech .= " Sys_id is ".$sys_id;
		$speech .= "\r\n";
		
		$speech .= " Thanks for contacting us. Are you satisfied with the response?";
		//echo $speech;
		

	}
	if($json->queryResult->intent->displayName=='Get_Status_ticket'||$json->queryResult->intent->displayName=='Get_Status_ticket - ticketinputagain')
	{
		
		if(isset($json->queryResult->parameters->Raisedate))
		{ $Raisedate = $json->queryResult->parameters->Raisedate; }
		
		if(isset($json->queryResult->parameters->Ticketno))
		{ $Ticketno = $json->queryResult->parameters->Ticketno; }
		str_pad($Ticketno, 7, '0', STR_PAD_LEFT);
		$Raisedate = substr($Raisedate, 0, 10);
			
		$instance = "dev75823";
		$username = "admin";
		$password = "Ctli1234";
		$table = "incident";
		
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=getRecords&sysparm_query=numberENDSWITH".$Ticketno."^sys_created_onSTARTSWITH".$Raisedate;
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$assigned_to =  $jsonoutput->records[0]->assigned_to;
		$number =  $jsonoutput->records[0]->number;
		$state =  $jsonoutput->records[0]->state;
		$sys_updated_by = $jsonoutput->records[0]->sys_updated_by;
		$sys_updated_on = $jsonoutput->records[0]->sys_updated_on;
		$short_description = $jsonoutput->records[0]->short_description;
		
		
		if($assigned_to=='')
		{
			$assigned_to = 'no one';
		}
		
		switch($state){
		    case 1:
			$dis_state = "New";
			break;
		    case 2:
			$dis_state = "In Progress";
			break;
		    case 3:
			$dis_state = "On Hold";
			break;
		    case 7:
			$dis_state = "Closed";
			break;
		   
		}

		$speech = "Incident ".$number." is currently assigned to ".$assigned_to.". Current status of  the incident is ".$dis_state." . This incident was last updated by ".$sys_updated_by." on ".$sys_updated_on;
		$speech .= " The incident was raised for the issue ".$short_description;
		if($number == ''){ $speech="";}	
		
		//$speech = "Thanks ".$name."! Incident Created Successfully for issue " . $sh_desc . " and your incident number is " . $incident_no;
		//echo $speech;
		

	}
	if($json->queryResult->intent->displayName=='Raise_ticket_intent - GetnameGetissue - yes - custom')
	{
		if(isset($json->queryResult->parameters->ticket_num))
		{ $ticket_num = $json->queryResult->parameters->ticket_num; }
		str_pad($ticket_num, 7, '0', STR_PAD_LEFT);
		
		//{"incident_state":"7","close_notes":"Resolved by Caller","close_code":"Closed/Resolved by Caller","caller_id":"System Administrator"}
		//$sh_desc = "Testing";
		//$name = "someone";
		$instance = "dev75823";
		$username = "admin";
		$password = "Ctli1234";
		$table = "incident";
		
		/*$jsonobj = array(
					'incident_state' => '7'
					'close_notes' => 'Resolved by Caller'
					'close_code' => 'Closed/Resolved by Caller'
					'caller_id' => 'System Administrator'
				);
             	$jsonobj = json_encode($jsonobj);*/	
		$jsonobj=1;
		
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=update&sysparm_query=numberENDSWITH".$ticket_num;
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"incident_state\":\"7\",\"close_notes\":\"Resolved by Caller\",\"close_code\":\"Closed/Resolved by Caller\",\"caller_id\":\"System Administrator\"}");
		}
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$sh_desc =  $jsonoutput->records[0]->short_description;
		$inc_num =  $jsonoutput->records[0]->number;
		$speech = "Thanks! Incident ".$inc_num." closed Successfully for issue " . $sh_desc;
		$speech .= " Thanks for contacting us!";
		//echo $speech;
		
		
	}
	if($json->queryResult->intent->displayName=='SCT_UnlockSapAccount - no - yes')
	{
		
		if(isset($json->queryResult->parameters->line_manager))
		{ $line_manager = $json->queryResult->parameters->line_manager; }
		
		
			
		$instance = "dev75823";
		$username = "admin";
		$password = "Ctli1234";
		
		
		$query = "https://$instance.service-now.com/api/sn_sc/v1/servicecatalog/items/0bd1963f4f02230017ab4f00a310c7bd/order_now";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$jsonvar = array('sysparm_quantity'=> '1',
				 'variables'=>	array('line_manager_name' => $line_manager
				  			
						     )
				);
             	$jsonvar = json_encode($jsonvar);
		
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
		$response=curl_exec($curl);
		
		curl_close($curl);
		//echo $response;
		//$jsonoutput = json_decode($response);
		//echo $jsonoutput;
	//	$item_name =  $jsonoutput->result->items[0]->item_name;
		
		
		/*$query = "https://dev55842.service-now.com/api/sn_sc/v1/servicecatalog/cart/submit_order";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

		$response = curl_exec($curl);
		echo $response;
		curl_close($curl);*/
		$jsonoutput = json_decode($response);
		//echo $jsonoutput;
		$request_num =  $jsonoutput->result->request_number;
		$speech = "Your Request number is ".$request_num." Please attach approval of your Line Manager to the ticket, so that your account will be unlocked";
 
		

	}
	if($json->queryResult->intent->displayName=='SCT_DeactivateAccount - no - yes')
	{
		
		if(isset($json->queryResult->parameters->line_manager))
		{ $line_manager_name = $json->queryResult->parameters->line_manager; }
		
		if(isset($json->queryResult->parameters->deactivation_date))
		{ $effective_date = $json->queryResult->parameters->deactivation_date; }
		$effective_date=substr($effective_date, 0, 10);
		
		
			
		$instance = "dev75823";
		$username = "admin";
		$password = "Ctli1234";
		
		
		$query = "https://$instance.service-now.com/api/sn_sc/v1/servicecatalog/items/e4d504654f12230017ab4f00a310c706/order_now";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$jsonvar = array('sysparm_quantity'=> '1',
				 'variables'=>	array('line_manager_name' => $line_manager_name,
				  			'effective_date'=> $effective_date
						     )
				);
             	$jsonvar = json_encode($jsonvar);
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
		$response=curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$request_num =  $jsonoutput->result->request_number;
		$speech = "Your Request number is ".$request_num." Please attach approval of your Line Manager to the ticket, so that the account will be deactivated";
 	}
  ///----SNOW ENDS HERE-----
///-----RPA-----///
	if($json->queryResult->intent->displayName=='GuessNumber')
	{
		//GET Authentication Token
		
		
		$query = "https://platform.uipath.com/api/Account/Authenticate";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$jsonvar = array('tenancyName'=> 'rachna2019',
				 'usernameOrEmailAddress'=>'rachnarke@gmail.com',
				 'password'=>'@viK@123',
				 'url'=>'https://platform.uipath.com/'
				);
             	$jsonvar = json_encode($jsonvar);
		
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
		$response=curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		//echo $jsonoutput;
		$AuthToken =  $jsonoutput->result;
		//$speech = "Your Auth number is ".$AuthToken;
		
		//Get release key of process
		$query = "https://platform.uipath.com/odata/Releases";
		$curl = curl_init($query);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$authorization = "Authorization: Bearer ".$AuthToken; // Prepare the authorisation token
      		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$ReleaseKey =  $jsonoutput->value[0]->Key;
		//$speech .= " Your release key is ".$ReleaseKey;
		
		
		
		//GET ROBOT ID
		//https://platform.uipath.com/odata/Robots?$top=1&$filter=Name eq 'guessnum'
		$robotname = "guessnum";
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://platform.uipath.com/odata/Robots?$top=1&$filter=Name%20eq%20%27guessnum%27",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer $AuthToken",
		    "Content-Type: application/json",

		  ),
		));

		$response = curl_exec($curl);

		$jsonoutput = json_decode($response);
				//echo 'jsonoutput '.$jsonoutput;
				$RobotId =  $jsonoutput->value[0]->Id;
		curl_close($curl);

		
		
		//START A JOB
		$query = "https://platform.uipath.com/odata/Jobs/UiPath.Server.Configuration.OData.StartJobs";
		$curl = curl_init($query);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$authorization = "Authorization: Bearer ".$AuthToken; // Prepare the authorisation token
      		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
		$jsonvar = array (
 					 'startInfo' => 
  					array (
    						'ReleaseKey' => $ReleaseKey,
    						'Strategy' => 'Specific',
    						'RobotIds' => 
    						array (
      							0 => $RobotId,
    						       ),
    						'NoOfRobots' => 0,
    						'Source' => 'Manual',
  						),
				);
		
		$jsonvar = json_encode($jsonvar);
		
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			  //  curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
		$response=curl_exec($curl);
		curl_close($curl);
		//$jsonoutput = json_decode($response);
		//echo $jsonoutput;
		$speech .= ' Starting the game...';
		
		
		
		
		
		
 
		
	}
	
	//RPA another usecase Weather info of country
	if($json->queryResult->intent->displayName=='CountryWeather')
	{
		//GET Authentication Token
		
		if(isset($json->queryResult->parameters->country))
		{	$geo_country= $json->queryResult->parameters->country; } 
		
		$query = "https://platform.uipath.com/api/Account/Authenticate";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$jsonvar = array('tenancyName'=> 'rachna2019',
				 'usernameOrEmailAddress'=>'rachnarke@gmail.com',
				 'password'=>'@viK@123',
				 'url'=>'https://platform.uipath.com/'
				);
             	$jsonvar = json_encode($jsonvar);
		
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
		$response=curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		//echo $jsonoutput;
		$AuthToken =  $jsonoutput->result;
		//$speech = "Your Auth number is ".$AuthToken;
		
		//Get release key of process
		$query = "https://platform.uipath.com/odata/Releases";
		$curl = curl_init($query);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$authorization = "Authorization: Bearer ".$AuthToken; // Prepare the authorisation token
      		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$ReleaseKey =  $jsonoutput->value[0]->Key;
		//$speech .= " Your release key is ".$ReleaseKey;
		
		
		
		//GET ROBOT ID
		//https://platform.uipath.com/odata/Robots?$top=1&$filter=Name eq 'guessnum'
		$robotname = "CTLI_Robot";
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://platform.uipath.com/odata/Robots?$top=1&$filter=Name%20eq%20%27CTLI_Robot%27",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer $AuthToken",
		    "Content-Type: application/json",

		  ),
		));

		$response = curl_exec($curl);

		$jsonoutput = json_decode($response);
				//echo 'jsonoutput '.$jsonoutput;
				$RobotId =  $jsonoutput->value[0]->Id;
		//$speech = "Robot id ".$RobotId;
		curl_close($curl);
	
		
	//START A JOB
		//$geo_country=$json->queryResult->parameters->country;
		//echo $geo_country;
		$query = "https://platform.uipath.com/odata/Jobs/UiPath.Server.Configuration.OData.StartJobs";
		$curl = curl_init($query);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$authorization = "Authorization: Bearer ".$AuthToken; // Prepare the authorisation token
      		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
		$jsonvar = array (
					  'startInfo' => 
					  array (
					    'ReleaseKey' => $ReleaseKey,
					    'Strategy' => 'Specific',
					    'RobotIds' => 
					    array (
					      0 => $RobotId,
					    ),
					    'NoOfRobots' => 0,
					    'Source' => 'Manual',
						//  \"InputArguments\":\"{\\\"Country\\\":\\\"china\\\"}\"
					    'InputArguments' => "{\"Country\":\"$geo_country\"}",
					  ),
				);
			
			
			
			
		$jsonvar = json_encode($jsonvar);
		//echo $jsonvar;
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			  //  curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
	$response=curl_exec($curl);
		curl_close($curl);
		//$jsonoutput = json_decode($response);
		//echo $jsonoutput
		$speech .= ' Ok. I am getting';
		
		
		
		
		
		
 
		
	}
	
	//efashion implementation starts here
	// Fetching values from dialogflow
	if(isset($json->queryResult->parameters->command))
		{	$com = $json->queryResult->parameters->command; } else {$com = "";}
	
	$com = strtolower($com);
	// action works with previousintent and topbottom intent
	if(isset($json->queryResult->parameters->myaction))
		{	$myaction = $json->queryResult->parameters->myaction; } else {$myaction = '0';}
	
	if(isset($json->queryResult->action))
		{	$action = $json->queryResult->action; } else {$action = '0';}
	
	//checking for correct intent (topbottom or other)
	if($action == 'MyPreviousIntent' and $myaction == 'HighLowValues' )
	{
		if($com != "")
		{ $action = ""; }
		else
		{$action = 'HighLowValues';
				
		}
	}
	//echo $my_action;
	//echo $action;
	if(($com == 'liststates' || $com == 'shoplist' || $com == 'listcity' || $com == 'listfamily' || $com == 'listcategory' || $com == 'listarticle' || $com == 'listyear') && $myaction == 'amountsold' && $action == 'MyPreviousIntent')
	{$com = "amountsold";}
	
	if(($com == 'liststates' || $com == 'shoplist' || $com == 'listcity' || $com == 'listfamily' || $com == 'listcategory' || $com == 'listarticle' || $com == 'listyear') && $myaction == 'qtysold' && $action == 'MyPreviousIntent' )
	{$com = "qtysold";}
	 
	if(($com == 'liststates' || $com == 'shoplist' || $com == 'listcity' || $com == 'listfamily' || $com == 'listcategory' || $com == 'listarticle' || $com == 'listyear') && $myaction == 'margin' && $action == 'MyPreviousIntent')
	{$com = "margin";}
	//echo $com;
	//to execute xsjs for high and low measures
	$xsjs_url = "http://74.201.240.43:8000/ChatBot/Sample_chatbot/EFASHION_DEV_TOP.xsjs?";
		
	//fetching other values from dialogflow
		if(isset($json->queryResult->parameters->STATE))
		{	$STATE= $json->queryResult->parameters->STATE; } 
	
		if(isset($json->queryResult->parameters->ENT_STATE))
		{	$ENT_STATE= $json->queryResult->parameters->ENT_STATE;	}
	
		if(isset($json->queryResult->parameters->CITY))
		{	$CITY= $json->queryResult->parameters->CITY; } 
	
		if(isset($json->queryResult->parameters->ENT_CITY))
		{	$ENT_CITY= $json->queryResult->parameters->ENT_CITY; } 
	
		if(isset($json->queryResult->parameters->SHOPNAME))
		{	$SHOPNAME= $json->queryResult->parameters->SHOPNAME; }
	
		if(isset($json->queryResult->parameters->ENT_SHOP))
		{	$ENT_SHOP= $json->queryResult->parameters->ENT_SHOP; }
	
		if(isset($json->queryResult->parameters->YR))
		{	$YR= $json->queryResult->parameters->YR; } 
		
		if(isset($json->queryResult->parameters->QTR))
		{	$QTR= $json->queryResult->parameters->QTR; }
		
		if(isset($json->queryResult->parameters->MTH))
		{	$MTH= $json->queryResult->parameters->MTH; } 

	   	if(isset($json->queryResult->parameters->FAMILY))
		{	$FAMILY= $json->queryResult->parameters->FAMILY; } 
	
		if(isset($json->queryResult->parameters->ENT_FAM))
		{	$ENT_FAM= $json->queryResult->parameters->ENT_FAM; } 
	
	     	if(isset($json->queryResult->parameters->CATEGORY))
		{	$CATEGORY= $json->queryResult->parameters->CATEGORY; } 
	
		if(isset($json->queryResult->parameters->ENT_CAT))
		{	$ENT_CAT= $json->queryResult->parameters->ENT_CAT; }
	
	     	if(isset($json->queryResult->parameters->ARTICLE))
		{	$ARTICLE= $json->queryResult->parameters->ARTICLE; } 
	
		if(isset($json->queryResult->parameters->ENT_ARTICLE))
		{	$ENT_ARTICLE= $json->queryResult->parameters->ENT_ARTICLE; }
	
		if(isset($json->queryResult->parameters->NUM))
		{	$NUM= $json->queryResult->parameters->NUM;
			
		}

		if(isset($json->queryResult->parameters->ENT_TOP_BOT))
		{	$ENT_TOP_BOT= $json->queryResult->parameters->ENT_TOP_BOT; } 
		
		if(isset($json->queryResult->parameters->ENT_MEASURE))
		{	$ENT_MEASURE= $json->queryResult->parameters->ENT_MEASURE; }
	
		$CITY= strtoupper($CITY);
		$STATE= strtoupper($STATE);
		$SHOPNAME= strtoupper($SHOPNAME);
		$FAMILY= strtoupper($FAMILY);
		$CATEGORY= strtoupper($CATEGORY);
		$ARTICLE= strtoupper($ARTICLE);
		$YR= strtoupper($YR);
		$MTH= strtoupper($MTH);
		$QTR= strtoupper($QTR);
		$ENT_MEASURE= strtoupper($ENT_MEASURE);
		$ENT_TOP_BOT= strtoupper($ENT_TOP_BOT);
	
	//removing space between strings
		$SHOPNAME = str_replace(' ', '', $SHOPNAME);
		$CITY = str_replace(' ', '', $CITY);
		$STATE = str_replace(' ', '', $STATE);
		$FAMILY = str_replace(' ', '', $FAMILY);
		$CATEGORY = str_replace(' ', '', $CATEGORY);
		$ARTICLE = str_replace(' ', '', $ARTICLE);
		$YR = str_replace(' ', '', $YR);
		$MTH = str_replace(' ', '', $MTH);
		$QTR = str_replace(' ', '', $QTR);
	$ENT_MEASURE = str_replace(' ', '', $ENT_MEASURE);
		
	//to display qty correctly. setting up flag 
		$qty_array = array("QUANTITY","QTY","ITEMS","PRODUCTS");
		if (in_array($ENT_MEASURE, $qty_array)) {$showqty=1;}
	
	
		
	
		$userespnose = array("PLEASEIGNORE", "IGNORE","IGNOREIT", "ANYVALUE","ANY","NOIDEA","DRILLUP");
		
		if (in_array($YR, $userespnose)) {$YR='0';}
		if (in_array($QTR, $userespnose)) {$QTR='0';}
		if (in_array($MTH, $userespnose)) {$MTH='0';}
	
		$useres = array("PLEASEIGNORE", "IGNORE","IGNOREIT", "DRILLUP");
		if (in_array($STATE, $useres)) {$STATE=""; $ENT_STATE ="";}
		if (in_array($CITY, $useres)) {$CITY=""; $ENT_CITY ="";}
		if (in_array($SHOPNAME, $useres)) {$SHOPNAME=""; $ENT_SHOP ="";}
		if (in_array($FAMILY, $useres)) {$FAMILY=""; $ENT_FAM ="";}
		if (in_array($CATEGORY, $useres)) {$CATEGORY=""; $ENT_CAT ="";}
		if (in_array($ARTICLE, $useres)) {$ARTICLE=""; $ENT_ARTICLE ="";}   
		
		$drillarray = array("DRILLDOWN", "WHATABOUT", "HOWABOUT","VALUESONLYFOR", "VALUESFORONLY", "VALUESFOR");
		if (in_array($STATE, $drillarray)) {$STATE=""; $ENT_STATE = "state"; }
		if (in_array($CITY, $drillarray)) {$CITY=""; $ENT_CITY = "city";}
		if (in_array($SHOPNAME, $drillarray)) {$SHOPNAME=""; $ENT_SHOP = "shop";}
		if (in_array($FAMILY, $drillarray)) {$FAMILY=""; $ENT_FAM = "family";}
		if (in_array($CATEGORY, $drillarray)) {$CATEGORY=""; $ENT_CAT = "category"; }
		if (in_array($ARTICLE, $drillarray)) {$ARTICLE=""; $ENT_ARTICLE = "article";} 
	
		 
		    
		$userespnose = array("EACH", "EVERY","ALL");
		if(in_array($YR, $userespnose))	{ $YR = 'ALL';	}
		if(in_array($QTR, $userespnose)){ $QTR = 'ALL';	}
		if(in_array($MTH, $userespnose)){ $MTH = 'ALL';	}
		if(in_array($CITY, $userespnose)){ $CITY = 'ALL'; }
		if(in_array($FAMILY, $userespnose)){ $FAMILY = 'ALL'; }
		if(in_array($CATEGORY, $userespnose)){	$CATEGORY = 'ALL';}
		if(in_array($ARTICLE, $userespnose)){	$ARTICLE = 'ALL'; }
		if(in_array($STATE, $userespnose)){	$STATE = 'ALL'; }
		if(in_array($SHOPNAME, $userespnose)){	$SHOPNAME = 'ALL'; }
	
		if($CITY != "" and $CITY != 'ALL' )
		{ 
			$xsjs_url .= "&CITY=$CITY"; 
			$ENT_CITY = "city";
		}
	
		if($STATE!="" and $STATE !='ALL' )
		 { 
			$xsjs_url .= "&STATE=$STATE";
		  	$ENT_STATE = "state";
		 }
	
		if($SHOPNAME!="" and $SHOPNAME != 'ALL' )
		{
			$xsjs_url .= "&SHOPNAME=$SHOPNAME"; 
			$ENT_SHOP = "shop";
		}
	
		if($FAMILY!="" and $FAMILY != 'ALL' )
		{ 
			$xsjs_url .= "&FAMILY=$FAMILY";
			$ENT_FAM = "family";
		}
	
		if($CATEGORY!="" and $CATEGORY != 'ALL' )
		{ 
			$xsjs_url .= "&CATEGORY=$CATEGORY"; 
			$ENT_CAT = "category";
		}
	
		if($ARTICLE!="" and $ARTICLE != 'ALL')	
		 { 
			$xsjs_url .= "&ARTICLE=$ARTICLE"; 
		 	$ENT_ARTICLE = "article";
		 }
		if($action == 'showallvalues' or $myaction == 'showallvalues')
		{
			$salemeasure = array("SALES","SALE");
			if(in_array($ENT_MEASURE, $salemeasure)){$com = "amountsold"; }
			$marginmeasure = array("MARGIN","PROFIT");
			if(in_array($ENT_MEASURE, $marginmeasure)){$com = "margin"; }
			$qtymeasure = array("QUANTITY","QTY","ITEMS","PRODUCTS");
			if(in_array($ENT_MEASURE, $qtymeasure)){$com = "qtysold"; }
		}
		if($com!="")	
		 { 
			$xsjs_url .= "&COMMAND=$com";
		 	
		 }
	
		if($YR=="" )		{	$YR='0'; 	}
		if($MTH=="" )		{	$MTH='0';	}
		if($QTR=="" )		{	$QTR='0'; 	}
		if($action!="" )	 { $xsjs_url .= "&ACTION=$action"; }
		if($ENT_CITY!="" )	 { $xsjs_url .= "&ENT_CITY=$ENT_CITY"; }
		if($ENT_STATE!="" )	 { $xsjs_url .= "&ENT_STATE=$ENT_STATE"; }
		if($ENT_SHOP!="" )	 { $xsjs_url .= "&ENT_SHOP=$ENT_SHOP"; }
		if($ENT_FAM!="")	{ $xsjs_url .= "&ENT_FAM=$ENT_FAM"; }
		if($ENT_CAT!="")	{ $xsjs_url .= "&ENT_CAT=$ENT_CAT"; }
		if($ENT_ARTICLE!="" )	{ $xsjs_url .= "&ENT_ARTICLE=$ENT_ARTICLE"; }
		if($ENT_TOP_BOT!="")	{ $xsjs_url .= "&ENT_TOP_BOT=$ENT_TOP_BOT"; }
		if($ENT_MEASURE!="" )	 { $xsjs_url .= "&ENT_MEASURE=$ENT_MEASURE";}
		if($NUM == "") 		{	$NUM='0'; } 
		$top_array =  array("HIGHEST","MAXIMUM","LOWEST","MINIMUM");
		if (in_array($ENT_TOP_BOT, $top_array)|| $NUM == 1) 
		{
			$NUM=1;
			$disnum = "";
			$disval = "VALUE IS ";
		}
		else 
		{
			$disnum=$NUM;
			$disval = "VALUES ARE ";
		}
		/*$xsjs_url .= "&CITY=$CITY";
		
		
		$xsjs_url .= "&STATE=$STATE";
		$xsjs_url .= "&SHOPNAME=$SHOPNAME";
		$xsjs_url .= "&FAMILY=$FAMILY";
		$xsjs_url .= "&CATEGORY=$CATEGORY";
		$xsjs_url .= "&ARTICLE=$ARTICLE";*/
		$xsjs_url .= "&YR=$YR";
		$xsjs_url .= "&MTH=$MTH";
		$xsjs_url .= "&QTR=$QTR";
		$xsjs_url .= "&NUM=$NUM";
		
	//echo $xsjs_url;
		
			$username    = "SANYAM_K";
			$password    = "Sanyam@1234";
			$ch      = curl_init( $xsjs_url );
			$options = array(
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERPWD        => "{$username}:{$password}",
			CURLOPT_HTTPHEADER     => array( "Accept: application/json" ),
			);
			curl_setopt_array( $ch, $options );
			$json = curl_exec( $ch );
			$someobj = json_decode($json,true);
		
	
	$salemeasure = array("SALES","SALE");
	if(in_array($ENT_MEASURE, $salemeasure)){$com = "amountsold"; }
	$marginmeasure = array("MARGIN","PROFIT");
	if(in_array($ENT_MEASURE, $marginmeasure)){$com = "margin"; }
	$qtymeasure = array("QUANTITY","QTY","ITEMS","PRODUCTS");
	if(in_array($ENT_MEASURE, $qtymeasure)){$com = "qtysold"; }
	
		if($com == 'amountsold' or $com == 'margin' or $com == 'qtysold' or $action == 'HighLowValues'  )
		{
			
			if ($com == 'amountsold' )
			{
				$distext = "We recorded sales ";
				//$distext .= "\r\n";
				$show_dlr = "worth of $";
			}
			else if($com == 'margin' )
			{
				$distext = "We recorded margin ";
				//$distext .= "\r\n";
				$show_dlr = "worth of $";
			}
			else if ($com == 'qtysold' )
			{
				$distext = "# Units Sold =  ";
				//$distext .= "\r\n";
				$show_dlr = "";
			}
			if ($action == 'HighLowValues')
			{
				
				$distext = "$ENT_TOP_BOT $disnum $ENT_MEASURE $disval ";
				if($showqty==1)
				{$show_dlr = "";} else {$show_dlr = "worth $";}
				 $distext .= "\r\n";
				$speech .= $distext;
				$distext="";
				
			}
			if($CITY !="" 	|| $ENT_CITY !="")	{ $discity = " for city "; } else { $discity = ""; }
			if($STATE !="" || $ENT_STATE !=""){ $disstate = " in state of "; } else { $disstate = ""; }
			if($FAMILY !="" || $ENT_FAM !=""){ $disfamily = ", For product family : "; } else {$disfamily = ""; }
            		if($CATEGORY !="" || $ENT_CAT !=""){ $discategory = ", For category : "; }	else { $discategory = ""; }
            		if($ARTICLE !="" || $ENT_ARTICLE !=""){$disarticle = ", For article : ";} else	{ $disarticle = ""; }
			if($SHOPNAME !="" || $ENT_SHOP !="") { $disshop = ", For shop :  "; } else{	$disshop = "";	}
			if($YR != '0' || $QTR != '0' || $MTH != '0')	{      $distimeframe = ", Time Frame : ";} else {$distimeframe = "";}
			if($QTR != '0')	{      $disqtr = "Q";} else {$disqtr = "";}
			//if($MTH != '0')	{      $dismth = " for month ";} else {$dismth = "";}
			
			
			//We recorded sales worth $25000.6 in state of Texas. For Product family : Overcoats, Time frame: Q4 2001.
	//e.	# Units Sold = 33  for Houston. Product family: Overcoats, Timeframe: 10.2001 
			foreach ($someobj["results"] as $value) 
			{
				$speech .=  $distext.$show_dlr.$value["AMOUNT"].$discity.$value["CITY"].$disstate.$value["STATE"].$disshop.$value["SHOP_NAME"].$disfamily.$value["FAMILY_NAME"].$discategory.$value["CATEGORY"].$disarticle.$value["ARTICLE_LABEL"].$distimeframe.$disqtr.$value["QTR"]." ".$value["MTH"]." ".$value["YR"];
				$speech .= "\r\n";
				//$speech .= "Do you want this info on mail\n";
			 }
			//if($speech != "") { $speech .= "I can drill down further\n";}
		}
		else if($com == 'shoplist')
		{
			foreach ($someobj["results"] as $value) 
			{
				$speech .= $value["SHOP_NAME"];
				$speech .= "\r\n";
			 }
		}
		else if ($com == 'liststates')
		{
			$speech = "You can see values for following states";
			$speech .= "\r\n";
			foreach ($someobj["results"] as $value) 
			{
				
				$speech .= $value["STATE"]." - ".$value["SHORT_STATE"];
				$speech .= "\r\n";
			}
			$speech .= "Which would you prefer?";
			
		}
		else if ($com == 'listcity')
		{
			$speech = "You can see values for following cities";
			$speech .= "\r\n";
			foreach ($someobj["results"] as $value) 
			{
				
				$speech .= $value["CITY"];
				$speech .= "\r\n";
			}
			$speech .= "Which would you prefer?";
			
		}
		else if ($com == 'listfamily')
		{
			$speech = "You can see values for following Product Families";
			$speech .= "\r\n";
			foreach ($someobj["results"] as $value) 
			{
				
				$speech .= $value["FAMILY_NAME"];
				$speech .= "\r\n";
			}
			$speech .= "Which would you prefer?";
			
		}
		else if ($com == 'listcategory')
		{
			$speech = "You can see values for following Product categories";
			$speech .= "\r\n";
			foreach ($someobj["results"] as $value) 
			{
				
				$speech .= $value["CATEGORY"];
				$speech .= "\r\n";
			}
			$speech .= "Which would you prefer?";
			
		}
		else if ($com == 'listarticle')
		{
			$speech = "You can see values for following Product articles";
			$speech .= "\r\n";
			foreach ($someobj["results"] as $value) 
			{
				
				$speech .= $value["ARTICLE_LABEL"];
				$speech .= "\r\n";
			}
			$speech .= "Which would you prefer?";
			
		}
		else if ($com == 'listyear')
		{
			$speech = "You can see values for following years";
			$speech .= "\r\n";
			foreach ($someobj["results"] as $value) 
			{
				
				$speech .= $value["YR"];
				$speech .= "\r\n";
			}
			$speech .= "Which would you prefer?";
			
		}
	
	else if ($com=='weather')
	{
			if(isset($json->queryResult->parameters->CITY))
		{	$CITY= $json->queryResult->parameters->CITY; } 
		if(strlen($CITY) > 1) 
		{	 

			/*$opts = array();
			$opts['http'] = array();
			$opts['http']['method']="GET";
			$opts['http']['header']="Accept-language: en\r\n"."Cookie: foo=bar\r\n";

			$t1=stream_context_create($opts);
		
			// Open the file using the HTTP headers set above
			$test_file=file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=$CITY&appid=4b75f2eaa9f9a62fe7309f06b84b69f9", false, $t1);
			
			$file = json_decode($test_file);
			$weather_data = $file->weather[0]->description;
			$temp =  1.8*($file->main->temp - 273) +32 ;
			$speech = "Now the Weather in $CITY is $weather_data , The temperature is $temp F " ;
			$speech .= "\r\n";*/
			//$link = "https://api.openweathermap.org/data/2.5/weather?q=".$CITY."&appid=4b75f2eaa9f9a62fe7309f06b84b69f9"; // Link goes here!
			$speech = "Now the Weather in $CITY can be seen at below link";
			$speech .= "\r\n";
			$link = "https://www.timeanddate.com/weather/usa/".$CITY;
			$speech .= $link;
			
				
			
		}
	}
	//efashion ends here
//---------------------------------------------------------------------------------
	

		
			
	
	$response1 = new \stdClass();
    	$response1->fulfillmentText = $speech;
    	$response1->source = "webhook";
	echo json_encode($response1);
}
else
{
	echo "Method not allowed";
}
?>
