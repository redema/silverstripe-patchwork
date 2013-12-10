
<% if $Pages %>
<ol class="breadcrumb">
	<% loop $Pages %>
	<% if $Last %>
	<li class="active">$MenuTitle</li>
	<% else %>
	<li class="link"><a href="$Link">$MenuTitle</a></li>
	<% end_if %>
	<% end_loop %>
</ol>
<% end_if %>
