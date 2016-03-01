<?php

//Generates a failure message 
	function fail($message, $errorNumber, $requiredErrorInfo){
	
		$requestorElement = $requiredErrorInfo['requestorElement'];
		$customerElement = $requiredErrorInfo['customerElement'];
		$reportElement = $requiredErrorInfo['reportElement'];
		$currentDate = $requiredErrorInfo['currentDate'];
		
		echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<ReportResponse ID="22" Created="$currentDate">
EOT;
		if($requestorElement && method_exists($requestorElement, 'asXML')) echo $requestorElement->asXML();
		if($customerElement  && method_exists($customerElement, 'asXML')) echo $customerElement->asXML();
		if($reportElement    && method_exists($reportElement, 'asXML')) echo $reportElement->asXML();
		echo <<<EOT
		<Report></Report>
		<Exception>
			<Number>$errorNumber</Number>
			<Severity>Crucial</Severity>
			<Message>$message</Message>
		</Exception>
	</Report>
</ReportResponse>
EOT;
	die();
	}
?>