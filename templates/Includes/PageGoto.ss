
<p class="page-goto">
	<a href="$Link" class="btn btn-default btn-sm">
		<span class="fa fa-arrow-right"></span> <% _t('PageSummary_ss.ReadMore', 'Read more') %>
	</a>
	<% if $Comments.class == 'DataList' %>
	<a href="$Link#comments-holder" class="btn btn-default btn-sm">
		<span class="fa fa-comment"></span> <% _t('PageSummary_ss.Comment', 'Comment') %>
		($Comments.Count)
	</a>
	<% end_if %>
</p>
