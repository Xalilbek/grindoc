<?php
session_start();
include_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/service/Option/Option.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$fin = get('fin');

try {
    if (strlen($fin) !== 7) {
        $user->error_msg('PIN nömrəni səhv daxil etdiz');
    }

    $data = getDataByFin($fin, [
        'Name',
        'SurName',
        'FatherName',
        'Photo',
        'Number',
        'DateOfBirth',
    ]);

    $user->success_msg('Ok', ['data' => $data]);
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}


function getDataByFin($fin, $fetchedData)
{
    global $user;

    $login = Service\Option\Option::getOrCreateValue('iamas_login', '');
    $pass  = Service\Option\Option::getOrCreateValue('iamas_pass', '');

    if ($login === "") {
        $user->error_msg('Access error');
    }

    $soapUrl = "https://eservice.e-health.gov.az/api/soap/Identity.svc";

    $XML = <<<XML
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   		<soapenv:Header>
	      <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
	         <wsse:UsernameToken wsu:Id="UsernameToken-906ED8673E6D1B1C5C154572621357318">
	            <wsse:Username>{$login}</wsse:Username>
	            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$pass}</wsse:Password>
	            <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">igLybsm1grE8hByIY7KE9Q==</wsse:Nonce>
	            <wsu:Created>2018-12-25T08:23:33.573Z</wsu:Created>
	         </wsse:UsernameToken>
	      </wsse:Security>
      	</soapenv:Header>
   		<soapenv:Body>
	      <tem:GetDocumentByNumber>
	         <!--Optional:-->
	         <tem:documentNumber xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	         <!--Optional:-->
	         <tem:documentType>IDCardForAdult</tem:documentType>
	         <!--Optional:-->
	         <tem:ByService>true</tem:ByService>
	         <!--Optional:-->
	         <tem:documentIssueDate xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	         <!--Optional:-->
	         <tem:documentSeries xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	         <!--Optional:-->
	         <tem:personPin>{$fin}</tem:personPin>
      		</tem:GetDocumentByNumber>
   		</soapenv:Body>
	</soapenv:Envelope>
XML;

    $xml_post_string = $XML;

    $headers = array(
        "Accept-Encoding: gzip,deflate",
        "Content-Type: text/xml;charset=UTF-8",
        "SOAPAction: \"http://tempuri.org/IIdentity/GetDocumentByNumber\"",
        "Content-Length: " . strlen($xml_post_string),
        "Host: eservice.e-health.gov.az",
        "Connection: Keep-Alive",
        "User-Agent: Apache-HttpClient/4.1.1 (java 1.5)",
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $soapUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    $result = null;

    if (FALSE !== $response) {
        $xml = simplexml_load_string($response);

        $xml->registerXPathNamespace("a", "http://schemas.datacontract.org/2004/07/HelperClassLibrary.ViewModel");

        $result = [];
        foreach ($fetchedData as $fetchedDatum) {
            $result[$fetchedDatum] = (string)$xml->xpath("//a:$fetchedDatum")[0][0];
        }
    }

    return $result;
}