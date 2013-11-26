
<div class="media">
	<% if $ShowThumbnail %>
	<a class="pull-left" href="$Link">
		<% if $PageSummaryThumbnail %>
		<% with $PageSummaryThumbnail.CroppedImage(64, 64) %>
		<img alt="$Title.ATTR" class="media-object img-thumbnail" src="$getURL" />
		<% end_with %>
		<% else %>
		<img alt="" class="media-object img-thumbnail" src="/patchwork/images/image-placeholder.png" />
		<% end_if %>
	</a>
	<% end_if %>
	<div class="media-body">
		<h4 class="media-heading">$PageSummaryTitle</h4>
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
