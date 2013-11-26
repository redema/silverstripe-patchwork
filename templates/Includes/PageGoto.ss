
<p class="page-goto">
	<a href="$Link" class="btn btn-default btn-sm">
		<span class="fa fa-arrow-right"></span> <% _t('PageSummary_ss.ReadMore', 'Read more') %>
	</a>
	<a href="$Link#comments-holder" class="btn btn-default btn-sm">
		<span class="fa fa-comment"></span> <% _t('PageSummary_ss.Comment', 'Comment') %>
		(<% if $Comments %>$Comments.Count<% else %>0<% end_if %>)
	</a>
</p>
