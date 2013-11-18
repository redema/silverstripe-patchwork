
<div class="page-labels">
	<% if $Categories.Count %>
	<p class="categories">
		<span class="sr-only"><% _t('PageSummary_ss.Categories', 'Categories') %></span>
		<% loop $Categories %>
		<a href="$SearchLink" class="label label-default"><span class="fa fa-folder"></span> $Title</a>
		<% end_loop %>
	</p>
	<% end_if %>
	<% if $Tags.Count %>
	<p class="tags">
		<span class="sr-only"><% _t('PageSummary_ss.Tags', 'Tags') %></span>
		<% loop $Tags %>
		<a href="$SearchLink" class="label label-default"><span class="fa fa-tag"></span> $Title</a>
		<% end_loop %>
	</p>
	<% end_if %>
</div>
