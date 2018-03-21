<body>
	<?php include 'email.header.php';?>
	
		<!-- email body -->
		<tr><td colspan='2'>Beste {{user_name}},</td></tr>
		<tr><td colspan='2'> </td></tr>
		<tr><td colspan='2'>De authenticatie token is geverifeerd. <br></td></tr>
		<tr><td colspan='2'>Login met jouw gebruikersnaam en het onderstaande wachtwoord op {{link}}</td></tr>
		<th style='border-bottom: 1px solid #ffb300; background: #eee;' colspan='3' align='left'>User</th>		
		<tr><td><b>Nieuw wachtwoord</b> </td><td>{{gen_password}}</td></tr>	
		<!-- / email body -->
		
	<?php include 'email.footer.php';?>
</body>