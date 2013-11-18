
<div class="container pageaggregate typography">
	<% if $Content.NoHTML != '' %>
	<div class="row">
		<div class="pageaggregate-content">
			$Content
		</div>
	</div>
	<% end_if %>
	<div class="row">
		<div class="col-md-4 col-md-push-8 pageaggregate-searchform">
			$SearchForm
			<div class="visible-sm visible-xs separator"></div>
		</div>
		<% if $PaginatedAggregatePages.Count %>
		<div class="col-md-8 col-md-pull-4 pageaggregate-pages">
			<% loop $PaginatedAggregatePages %>
			$Summary
			<% end_loop %>
			<% include Pagination PaginatedItems=$PaginatedAggregatePages %>
		</div>
		<% end_if %>
	</div>
</div>
