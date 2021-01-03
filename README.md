# Simple CRUD website
 A CRUD website back end development, with minimum front end coding. Uses php and PDO to integrate with a MySQL databse. 

All files uses the pdo variable, which is defined in the pdo.php ignored. The following happens in the pdo.php file:
```php

<?php
$pdo = new PDO('mysql:host=insert_your_host;port=XXXX;dbname=_databse_name_', '_username', '_password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
```

Line 9: ```PDO::ERRMODE_EXCEPTION``` - this is defined so that any erros in SQL statments that occurr in the server are sent back and displayed on the browser. This should be changed to ```PDO::ERRMODE_SILENT``` when testing is over. More on errors with PDO [here](http://www.w3big.com/php/php-pdo-error-handling.html)
