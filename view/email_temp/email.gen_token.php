<body>
	<?php include 'email.header.php';?>
	
		<!-- email body -->
		Beste {{user_name}},
		<br>
		<br>
		Open de onderstaande link om je wachtwoord te resetten. <br>
		Heb jij geen aanvraag gedaan. Klik dan niet op de link! De link vervalt automatisch na 10 minuten.<br>			
		{{recover_link}}
		<!-- / email body -->

	<?php include 'email.footer.php';?>
</body>