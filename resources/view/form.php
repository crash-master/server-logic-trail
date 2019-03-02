<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<? vjoin('test', ['name' => 'TESTING']) ?>
	<h1><?= $t ?></h1>
	<form action="<?= linkTo('IndexController@form_processing') ?>" method="post">
		<input type="hidden" name="submit-form">
		<input type="text" name="token" value="<?= \Kernel\Request::get_future_session_token() ?>">
		<input type="text" name="example"><br>
		<button>Submin</button>
	</form>
</body>
</html>