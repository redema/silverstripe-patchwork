
<%-- Args: $CaptionClass --%>

<% include Picturefill Title=$Title, Link=$Link, DesktopImage=$DesktopImage,
		TabletImage=$TabletImage, MobileImage=$MobileImage %>

<% if $Lead || $Content %>
<div class="$CaptionClass">
	$Lead
	$Content
</div>
<% end_if %>
