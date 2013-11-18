
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
		<div class="media-content">
			$PageSummaryContent
		</div>
		<p class="media-goto">
			<a href="$Link" class="btn btn-default btn-sm">
				<span class="fa fa-arrow-right"></span> <% _t('PageSummary_ss.ReadMore', 'Read more') %>
			</a>
			<a href="$Link#comments-holder" class="btn btn-default btn-sm">
				<span class="fa fa-comment"></span> <% _t('PageSummary_ss.Comment', 'Comment') %>
				(<% if $Comments %>$Comments.Count<% else %>0<% end_if %>)
			</a>
		</p>
		<% include PageLabels %>
		<div class="media-separator"></div>
	</div>
</div>
