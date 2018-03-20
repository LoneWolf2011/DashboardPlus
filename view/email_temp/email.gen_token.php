<body>
<?php include 'email.header.php';?>
<!-- email body -->
<tr><td>Beste {{user_name}},</td></tr>
<tr><td>Open de onderstaande link om je wachtwoord te resetten.</td></tr>
<tr><td>Heb jij geen aanvraag gedaan. Klik dan niet op de link! De link vervalt automatisch na 10 minuten.</td></tr>		
<tr><td>{{recover_link}}</td></tr>
<!-- / email body -->
<?php include 'email.footer.php';?>
</body>