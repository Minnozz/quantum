[[[MAIN]]]
<html>
	<head>
		<title>{_TITLE}</title>
	</head>
	<body>
		<h1>{_TITLE}</h1>
		{_STOCK}
	</body>
</html>
[[[/MAIN]]]

[[[STOCK_FILLED]]]
		<table id="stock">
	[[[ROW]]]
			<tr id="row-{_ROW}">
		[[[ITEM]]]
				<td id="cell-{_ROW}-{_COL}">{_NAME}</td>
		[[[/ITEM]]]
			</tr>
	[[[/ROW]]]
		</table>
[[[/STOCK_FILLED]]]

[[[STOCK_EMPTY]]]
		<p>The stock is currently empty</p>
[[[/STOCK_EMPTY]]]
