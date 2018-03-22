# DashboardPlus (Dark ANT version)
<p align="center">
<img src="img/app_logo_dark.png"  width="20%" align="center" /></p>

- Body class **dark-bg**

- Copy env.example.ini to env.ini
- Edit env.ini with DB settings for local DB and/or SCS DB, and local application settings
- Edit 1st line in /src/config/common.php:
 ```php
 $content = file_get_contents($_SERVER["DOCUMENT_ROOT"].'/mdb/env.ini')
 ```
- change **/mdb/** to match your root folder name.
- **HINT:** if your root folder is htdocs or html, remove **/mdb/**

- Edit **.htaccess** in root folder and change the urls so they match your folder structure
- If you want to use your own logo. Add your own image to the **/img/** folder and rename to **app_logo**.extension 

