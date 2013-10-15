
<%-- Args: $Title, $Link, $DesktopImage [, $MobileImage [, $TabletImage ]] --%>

<span class="item" data-picture="" data-alt="$Title.ATT" data-link="$Link">
	<span data-src="$DesktopImage.getURL"></span>
	<% if $MobileImage %>
	<span data-src="$MobileImage.getURL" data-media="(max-width: 768px)"></span>
	<% end_if %>
	<% if $TabletImage %>
	<span data-src="$TabletImage.getURL" data-media="(max-width: 1024px)"></span>
	<% end_if %>
	<noscript>
		<img src="$DesktopImage.getURL" alt="$Title.ATT" />
	</noscript>
</span>
