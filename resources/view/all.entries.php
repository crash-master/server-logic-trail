<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>All entries</title>
</head>
<body>
	<table>
		<thead>
			<tr>
				<td>ID</td>
				<td>USERNAME</td>
				<td>DATE_OF_UPDATE</td>
				<td>DATE_OF_CREATE</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($entries as $key => $entry): ?>
				<tr>
					<td><?= $entry['id'] ?></td>
					<td><?= $entry['username'] ?></td>
					<td><?= $entry['date_of_update'] ?></td>
					<td><?= $entry['date_of_create'] ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</body>
</html>