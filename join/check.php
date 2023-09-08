<?php
session_start();
require('../library.php');

// form配列に値が入っていたら$formに代入
if (isset($_SESSION['form'])) {
	$form = $_SESSION['form'];
} else {
// 直接URLを貼っても遷移できないようにする
	header('Location: index.php');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$db = dbconnect();
	// sqlでどのテーブルのどのカラムに値を格納するか
	$stmt = $db->prepare('insert into members (name, email, password, picture) VALUES (?, ?, ?, ?)');
	if (!$stmt) {
		die($db->error);
	}
	// パスワードをそのままDBに保存するのは危険なので暗号化させる
	$password = password_hash($form['password'], PASSWORD_DEFAULT);
	// ?に入れる値を指定
	$stmt->bind_param('ssss', $form['name'], $form['email'], $password, $form['image']);
	// 実行し上手く行かなかったらdieでエラーを表示
	$success = $stmt->execute();
	if (!$success) {
		die($db->error);
	}

	// 重複登録を避けるためにもセッションの引き継ぎを解除しておく
	unset($_SESSION['form']);
	header('Location: thanks.php');
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>会員登録</h1>
		</div>

		<div id="content">
			<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
			<form action="" method="post">
				<dl>
					<dt>ニックネーム</dt>
					<dd><?php echo h($form['name']); ?></dd>
					<dt>メールアドレス</dt>
					<dd><?php echo h($form['email']); ?></dd>
					<dt>パスワード</dt>
					<dd>
						【表示されません】
					</dd>
					<dt>写真など</dt>
					<dd>
						<img src="../member_picture/<?php echo h($form['image']); ?>" width="100" alt="" />
					</dd>
				</dl>
				<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
			</form>
		</div>

	</div>
</body>

</html>