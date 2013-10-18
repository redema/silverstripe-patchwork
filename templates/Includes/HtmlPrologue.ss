<!DOCTYPE html>
<!--[if lt IE 7]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="$ContentLocale" class="AnyPage $ClassName action-$getAction lt-ie9 lt-ie8 lt-ie7">
<![endif]-->
<!--[if IE 7]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="$ContentLocale" class="AnyPage $ClassName action-$getAction lt-ie9 lt-ie8">
<![endif]-->
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="$ContentLocale" class="AnyPage $ClassName action-$getAction lt-ie9">
<![endif]-->
<!--[if gt IE 8]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="$ContentLocale" class="AnyPage $ClassName action-$getAction">
<!--<![endif]-->
<head>
	<% base_tag %>
	<title><% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %><% if $SiteConfig %> | $SiteConfig.Title<% end_if %></title>
	$MetaTags(false)
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<![endif]-->
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
	<link rel="shortcut icon" href="/$MtimeCacheBuster('favicon.ico')" />
</head>
<body>
