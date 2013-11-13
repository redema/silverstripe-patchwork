
<div class="container">
	<% if $Content.NoHTML != '' %>
	<div class="pageaggregate-content">$Content</div>
	<% end_if %>
	<div class="pageaggregate-searchform">$SearchForm</div>
	<% if AggregatePages.Count %>
	<div class="pageaggregate-pages">
		<% loop AggregatePages %>
		$Summary
		<% end_loop %>
	</div>
	<% end_if %>
</div>
