<?php
$error = 'HTTP/1.1 403 Forbidden';
header($error);
print $error;