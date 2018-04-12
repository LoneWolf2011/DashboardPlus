<body>
	<?php include 'email.header.php';?>
	
		<!-- email body -->
		Beste {{user_name}},
		<br>
		<br>
		Welkom bij {{app_name}}.<br>
		Er is een account voor jou aangemaakt.	<br>	
		Log in met jouw mail adres en onderstaande wachtwoord {{login_link}}.<br>
		<br>			
		<b>Wachtwoord:</b> {{gen_password}}	
		<!-- / email body -->
	
	<?php include 'email.footer.php';?>
</body>