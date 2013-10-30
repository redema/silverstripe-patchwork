<%-- Args: $ID, $Items --%>

<% if $Items && $Items.Count %>
<div id="carousel-$ID" class="carousel slide items-$Items.Count" data-ride="carousel" aria-live="off">
	<ol class="carousel-indicators">
		<% loop $Items %>
		<li data-target="#carousel-$Top.ID" data-slide-to="$ActualPos($Pos)"
		 class="<% if $First %>active<% end_if %>"></li>
		<% end_loop %>
	</ol>
	<div class="carousel-inner">
		<% loop $Items %>
		<div class="item <% if $First %>active<% end_if %> $ExtraClasses">
			$Inner('carousel-caption')
		</div>
		<% end_loop %>
	</div>
	<a class="left carousel-control" href="#carousel-$ID" data-slide="prev">
		<span class="icon-prev"></span>
	</a>
	<a class="right carousel-control" href="#carousel-$ID" data-slide="next">
		<span class="icon-next"></span>
	</a>
</div>
<% end_if %>
