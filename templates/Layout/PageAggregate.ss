
<div class="container typography">
	<% if $Content.NoHTML != '' %>
	<div class="pageaggregate-content">$Content</div>
	<% end_if %>
	<div class="pageaggregate-searchform">$SearchForm</div>
	<% include Pagination PaginatedItems=$PaginatedAggregatePages %>
	<% if $PaginatedAggregatePages.Count %>
	<div class="pageaggregate-pages">
		<% loop $PaginatedAggregatePages %>
		$Summary
		<% end_loop %>
	</div>
	<% end_if %>
	<% include Pagination PaginatedItems=$PaginatedAggregatePages %>
</div>
