
<div class="media">
	<a class="pull-left" href="$Link">
		<% if $PageSummaryThumbnail %>
		<% with $PageSummaryThumbnail.CroppedImage(64, 64) %>
		<img alt="$Title.ATTR" class="media-object img-thumbnail" src="$getURL" />
		<% end_with %>
		<% else %>
		<img alt="" class="media-object img-thumbnail" src="/patchwork/images/image-placeholder.png" />
		<% end_if %>
	</a>
	<div class="media-body">
		<h4 class="media-heading">$PageSummaryTitle</h4>
		<p>$PageSummaryContent</p>
		<p><a href="$Link"><% _t('PageSummary_ss.ReadMore', 'Read more') %></a></p>
	</div>
</div>
