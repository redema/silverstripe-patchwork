
<%-- Args: $Title, $Link, $DesktopImage [, $MobileImage [, $TabletImage ]] --%>

<span class="picturefill" data-picture="" data-alt="$Title.ATT" data-link="$Link">
	<% if $DesktopImage.exists %>
	<noscript>
		<img src="$DesktopImage.getURL" alt="$Title.ATT" />
	</noscript>
	<span data-src="$DesktopImage.getURL"></span>
	<% end_if %>
	<% if $TabletImage.exists %>
	<span data-src="$TabletImage.getURL" data-media="(max-width: 1024px)"></span>
	<% end_if %>
	<% if $MobileImage.exists %>
	<span data-src="$MobileImage.getURL" data-media="(max-width: 768px)"></span>
	<% end_if %>
</span>
