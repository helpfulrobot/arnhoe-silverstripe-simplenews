<% with $NewsArticle %>
	$Title
	$Date
	<% if $NewsImage %>$NewsImage<% end_if %>

	$Content
<% end_with %>