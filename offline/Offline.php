<?php

/**
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * 
 * * Neither the name of Redema, nor the names of its contributors may be used
 *   to endorse or promote products derived from this software without specific
 *   prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * # Maintenance switch.
 * # 
 * RewriteCond %{REMOTE_ADDR} !127.0.0.1
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteRule .* patchwork/offline/Offline.php [L]
 */

header('HTTP/1.0 503 Service Unavailable');

$basedir = '../..';
$assetsdir = "$basedir/assets";
$err503file = "$assetsdir/error-503.html";

if (file_exists($err503file)) {
	die(file_get_contents($err503file));
}

$request = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<title>503 Service Unavailable</title>
	<meta name="generator" content="Patchwork" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php if (file_exists("$basedir/favicon.ico")) { ?>
	<link rel="shortcut icon" href="/favicon.ico" />
	<?php } ?>
	<style type = "text/css" media = "screen">
		body {
			padding: 1em;
		}
	</style>
</head>
<body>
	<h1>Service Unavailable</h1>
	<p>The page you are looking for is temporarily unavailable
	due to routine maintenance. Please try again later.</p>
	<p><em><?php echo $request; ?></em></p>
</body>
</html>
