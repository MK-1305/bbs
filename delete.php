<?php
session_start();
require('library.php');
// セッション情報がないとログイン画面に戻す処理
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit();
}

// idをURLで受け取るのでINPUT_GETになる
$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$post_id) {
    header('Location: index.php');
    exit();
}

$db = dbconnect();
// 何らかの事故で複数件削除されないようにdeleteを使うときはlimitを使う
$stmt = $db->prepare('delete from posts where id=? and member_id=? limit 1');
if (!$stmt) {
    die($db->error);
}
// urlで削除できないようにmember_idが自分ではない投稿は削除できないように指示
$stmt->bind_param('ii', $post_id, $id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}

header('Location: index.php'); exit();
?>
