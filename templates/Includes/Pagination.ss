
<%-- Args: $PaginatedItems [, $SizeClass ] --%>

<% if $PaginatedItems.MoreThanOnePage %>
<ul class="pagination $SizeClass">
	<% if $PaginatedItems.NotFirstPage %>
	<li><a href="$PaginatedItems.PrevLink">&laquo;</a></li>
	<% else %>
	<li class="disabled"><span>&laquo;</span></li>
	<% end_if %>
	
	<% loop $PaginatedItems.Pages %>
	<% if $CurrentBool %>
	<li class="active"><a href="#">$PageNum <span class="sr-only">(current)</a></li>
	<% else %>
	<li><a href="$Link">$PageNum</a></li>
	<% end_if %>
	<% end_loop %>
	
	<% if $PaginatedItems.NotLastPage %>
	<li><a href="$PaginatedItems.NextLink">&raquo;</a></li>
	<% else %>
	<li class="disabled"><span>&raquo;</span></li>
	<% end_if %>
</ul>
<% end_if %>
