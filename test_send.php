<?php

$to = "nsadikap@gmail.com, chad@esisalama.org";
$subject = "HTML email";
$dest = 'chad@esisalama.org';
$message = "
<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>This email contains HTML Tags!</p>
<table>
<tr>
<th>Firstname</th>
<th>Lastname</th>
</tr>
<tr>
<td>John</td>
<td>Doe</td>
</tr>
</table>
</body>
</html>
";
echo "TOUT VA BIEN $message";

$t=date('His');
$code = "$t[5]$t[3]$t[1]$t[0]$t[2]$t[4]";
echo "TOUT VA BIEN $message";
header("location://bulletin.delardc.com/send-mail?sujet=CODE SECRET&message=Le code SECRET EST:<h2>$code</h2>&dest=$dest&red=dgradru.delardc.com");

?> 