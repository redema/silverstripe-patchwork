
<div class="media">
	<% if $ShowBadge %>
	<a class="pull-left" href="$Link">
		<% if $Badge.ImgSrc %>
		<img alt="" class="media-object img-thumbnail" src="$Badge.ImgSrc" />
		<% else %>
		<img alt="" class="media-object img-thumbnail" src="/patchwork/images/image-placeholder.png" />
		<% end_if %>
	</a>
	<% end_if %>
	<div class="media-body">
		<{$TitleTag} class="media-heading">$PageSummaryTitle</{$TitleTag}>
		<div class="media-content">
			$PageSummaryContent
		</div>
		<% include PageGoto %>
		<% if $ShowPageLabels %>
		<% include PageLabels %>
		<% end_if %>
		<div class="media-separator"></div>
	</div>
</div>
