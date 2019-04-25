<?php
$method = $_SERVER['REQUEST_METHOD'];
//process only when method id post
if($method == 'POST')
{
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	
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
		$speech .= "BUKRS\tMandt\tMkoar\tBkont\tFromYear1\tFromPer1\tToYear1\tToPer2\n";
				
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
	if(isset($json->queryResult->parameters->AcctType))
	{ 
		$v_AcctType = $json->queryResult->parameters->AcctType; 
	  	$v_AcctType= strtoupper($v_AcctType);
	}
$curl = curl_init();
$url = "http://sealapp2.sealconsult.com:8000/sap/opu/odata/sap/ZFIN_POSTING_PERIODS_SRV/PostingPeriodsSet(Bukrs='".$v_CompanyCode."',Mkoar='".$v_AcctType."')/?"."\$format"."=json"

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
  CURLOPT_HTTPHEADER => array(
         "Authorization: Basic YXJ1bm46Y3RsQDE5NzY="
  ),
));

		$response = curl_exec($curl);
		//echo $response;
		$err = curl_error($curl);

		curl_close($curl);
		$jsonoutput = json_decode($response,true);
		$speech .= "BUKRS\tMandt\tMkoar\tBkont\tFromYear1\tFromPer1\tToYear1\tToPer2\n";
				
		
		   	$v_BUKRS = $jsonoutput->results->Bukrs;
			$v_Mandt = $jsonoutput->results->Mandt;
			$v_Mkoar = $jsonoutput->results->Mkoar;
			$v_Bkont = $jsonoutput->results->Bkont;
			$v_Frye1 = $jsonoutput->results->Frye1;
			$v_Frpe1 = $jsonoutput->results->Frpe1;
			$v_Toye1 = $jsonoutput->results->Toye1;
			$v_Tope1 = $jsonoutput->results->Tope1;
			
			
			$speech .=  $v_BUKRS."\t".$v_Mandt."\t".$v_Mkoar."\t\t".$v_Bkont."\t\t".$v_Frye1."\t\t".$v_Frpe1."\t\t".$v_Toye1."\t\t".$v_Tope1;
			$speech .= "\r\n";	
		
	}

//---------------------------------------------------
//--OPP DISPLAY SPECIFIC RECORD ENDS HERE
//---------------------------------------------------	
	
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
	
preg_match("/HTTP\/1.1(.*)/", $httpstatus, $res);
//echo $res[1];
	$v_res = str_replace(' ', '', $res[1]);
	if($v_res=="204NoContent")
	{
		$speech .= "Password Successfully changed.";
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
		$instance = "dev62236";
		$username = "admin";
		$password = "Ctli@234";
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
			
		$instance = "dev62236";
		$username = "admin";
		$password = "Ctli@234";
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
		$instance = "dev62236";
		$username = "admin";
		$password = "Ctli@234";
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
		
		
			
		$instance = "dev62236";
		$username = "admin";
		$password = "Ctli@234";
		
		
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
		
		
			
		$instance = "dev62236";
		$username = "admin";
		$password = "Ctli@234";
		
		
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
				 'password'=>'Avik.17.jan',
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
				 'password'=>'Avik.17.jan',
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
			$password    = "Welcome@234";
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
				$distext = "Sale  ";
				//$distext .= "\r\n";
				$show_dlr = "worth of $";
			}
			else if($com == 'margin' )
			{
				$distext = "Margin ";
				//$distext .= "\r\n";
				$show_dlr = "worth of $";
			}
			else if ($com == 'qtysold' )
			{
				$distext = "Products ";
				//$distext .= "\r\n";
				$show_dlr = "";
			}
			if ($action == 'HighLowValues')
			{
				
				$distext = "$ENT_TOP_BOT $disnum $ENT_MEASURE $disval ";
				if($showqty==1)
				{$show_dlr = "";} else {$show_dlr = "Worth of $";}
				 $distext .= "\r\n";
				$speech .= $distext;
				$distext="";
				
			}
			if($CITY !="" 	|| $ENT_CITY !="")	{ $discity = " for city "; } else { $discity = ""; }
			if($STATE !="" || $ENT_STATE !=""){ $disstate = " in state "; } else { $disstate = ""; }
			if($FAMILY !="" || $ENT_FAM !=""){ $disfamily = " family of product sold "; } else {$disfamily = ""; }
            		if($CATEGORY !="" || $ENT_CAT !=""){ $discategory = " category sold "; }	else { $discategory = ""; }
            		if($ARTICLE !="" || $ENT_ARTICLE !=""){$disarticle = " article sold ";} else	{ $disarticle = ""; }
			if($SHOPNAME !="" || $ENT_SHOP !="") { $disshop = " of shop "; } else{	$disshop = "";	}
			if($YR != '0')	{      $disyear = " for year ";} else {$disyear = "";}
			if($QTR != '0')	{      $disqtr = " in quarter ";} else {$disqtr = "";}
			if($MTH != '0')	{      $dismth = " for month ";} else {$dismth = "";}
			
			
			
			foreach ($someobj["results"] as $value) 
			{
				$speech .=  $distext.$show_dlr. $value["AMOUNT"].$disshop.$value["SHOP_NAME"].$discity.$value["CITY"].$disstate.$value["STATE"]." ".$value["FAMILY_NAME"].$disfamily." ".$value["CATEGORY"].$discategory." ".$value["ARTICLE_LABEL"].$disarticle.$disqtr.$value["QTR"].$dismth.$value["MTH"].$disyear.$value["YR"];
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
