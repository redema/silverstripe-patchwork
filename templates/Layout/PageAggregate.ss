
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
		<div class="col-md-8 col-md-pull-4 pageaggregate-pages">
			<p class="text-muted">
				<small>
					<%t PageAggregate_ss.Results "Found {num} pages ({time} sec)" num=$PaginatedAggregatePages.getTotalItems time=$SearchTime %>
				</small>
			</p>
			<% if $PaginatedAggregatePages.Count %>
			<% loop $PaginatedAggregatePages %>
			$Summary(1, 1, 'h3', 'Date')
			<% end_loop %>
			<% include Pagination PaginatedItems=$PaginatedAggregatePages %>
			<% end_if %>
		</div>
	</div>
</div>
