<?php 
/**
* AUTH -> AUDIT is an array of logging table definitions
* 
* 
*/


return array(
    'AUTH' => array(
		'AUDIT' => array(
			'CLIENT_LOG' => array(
				"DSN" => "JCORE",
				"table" => "client_log",
				"pk_field" => "client_log_pk",
				"pk" => 0,
				"fields" => array(
					"client_user_fk" => "",
					"client_fingerprint_fk" => "",
					"log_code" => "",
					"log_message" => "",
					"log_data" => "",
					"log_timestamp" => date("Y-m-d H:i:s"),
				),
			),		
		),
    ),
);

?>