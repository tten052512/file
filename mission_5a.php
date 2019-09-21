<!doctype html>
<html lang="ja">

<head>
     <meta charset="UTF-8">
     <title>mission5</title>
</head>

<body>

<?php	
//MySQLに接続
     $dsn ='データベース名';
     $user = 'ユーザー名';
     $password = 'パスワード';
     $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
     if ( $pdo != false ) {

//MySQLでテーブル作成
     $sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
     . "comment TEXT,"
     . "dt DATETIME"
	.");";
	$stmt = $pdo->query($sql);

/*    $sql ='SHOW CREATE TABLE mission5';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
         echo $row[1];
    }
    echo "<hr>";
*/

//MySQLにデータ挿入
if (!empty($_POST["name"]) && !empty($_POST["comment"])){
	if($_POST["passnew"]=="aa"){
          $name = $_POST["name"];
     
          $comment = $_POST["comment"];

          $dt = date("Y-m-d H:i:s");
     
//新規・編集の場合分け
          if(empty($_POST["editnocheck"])){//編集番号が入力されていないとき→新規書き込み
               $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, dt) VALUES (:name, :comment, :dt)");
               
          	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
               $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
               $sql -> bindParam(':dt', $dt, PDO::PARAM_STR);

               $sql -> execute();

          }else{//編集番号が空ではないとき→編集書き込み
               $editno = $_POST["editnocheck"];
               $idE = $editno;

               $sql = 'update mission5 set name=:name,comment=:comment,dt=:dt where id=:id';

               $stmt = $pdo->prepare($sql);
               $stmt->bindParam(':name', $name, PDO::PARAM_STR);
               $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
               $stmt->bindParam(':dt', $dt, PDO::PARAM_STR);
               $stmt->bindParam(':id', $idE, PDO::PARAM_INT);

               $stmt->execute();
          }     
	}elseif(empty($_POST["passnew"])){echo "新規投稿用パスワードが設定されていません";
	}elseif($_POST["passnew"]!="aa"){echo "正しい新規投稿用パスワードを入力してください";}
}elseif(isset($_POST["send"]) && empty($_POST["name"]) && empty($_POST["comment"])){echo "名前とコメントを入力してください";
}elseif(isset($_POST["send"]) && empty($_POST["name"])){echo"名前を入力してください";
}elseif(isset($_POST["send"]) && empty($_POST["comment"])){echo"コメントを入力してください";
}



//削除

if(!empty($_POST["delno"])){
	if($_POST["passdel"]=="dd"){
		$delete = $_POST["delno"];
          $idD = $delete;

          $sql = 'delete from mission5 where id=:id';
          $stmt = $pdo->prepare($sql);

          $stmt->bindParam(':id', $idD, PDO::PARAM_INT);
          $stmt->execute();
     
     }elseif(empty($_POST["passdel"])){echo "削除用パスワードが設定されていません";
     }elseif($_POST["passdel"]!="dd"){echo "正しい削除用パスワードを入力してください";}
}elseif(isset($_POST["delete"])){echo "削除番号が指定されていません";
}


//編集→編集したい番号だけの名前・コメントを投稿フォームに表示するための変数作り

if(!empty($_POST["editno"])){
	if($_POST["passedit"]=="ee"){
          $editno = $_POST["editno"];
          $sql = 'SELECT * FROM mission5 where id=:id';
          $stmt = $pdo->prepare($sql);

          $stmt->bindParam(':id', $editno, PDO::PARAM_INT);
          $stmt->execute();

          $results = $stmt->fetchAll();
          foreach ($results as $row){
               $editnumber = $row['id'];//ここで定義した$editnumberを編集番号確認フォームに送ることで$editnoが送信されていないときにはフォームが空になる
               $nameed     = $row['name'];
               $commented  = $row['comment'];
          }
	}elseif(empty($_POST["passedit"])){echo "編集用パスワードが設定されていません";
	}elseif($_POST["passedit"]!="ee"){echo "正しい編集用パスワードを入力してください";}
}elseif(isset($_POST["edit"])){echo "編集番号が指定されていません";
}

}else {
     echo "データベースの接続に失敗しました";
 }
?>

<h1>WEB掲示板</h1>
	<form action="" method="POST">


	<h2>コメントフォーム</h2>
          <p>名前:<input type="text" name="name" size="30" placeholder="氏名" autocomplete="off" value="<?php if(isset($nameed)) {echo $nameed;} ?>" autofocus></p>
		<p>コメント:<input type="text" name="comment" size="30" placeholder="コメント" autocomplete="off"value="<?php if(isset($commented)) {echo $commented;} ?>"> <input type="hidden"name="editnocheck" size="30" placeholder="No." autocomplete="off"value="<?php if(isset($editnumber)) {echo $editnumber;} ?>"></p>

     	<p>パスワード:<input type="password" name="passnew" placeholder="パスワード" autocomplete="off" ></p>

		<p><input type="submit" name="send" value="送信"></p>

     <h2>削除番号指定用フォーム</h2>
          <p>番号:<input type="text" name="delno" size="30" placeholder="No." autocomplete="off"></p>
               
		<p>パスワード:<input type="password" name="passdel" placeholder="パスワード" autocomplete="off" ></p>
               
		<p><input type="submit" name="delete" value="削除"></p>

     <h2>編集番号指定用フォーム</h2>
          <p>番号:<input type="text" name="editno" size="30" placeholder="No." autocomplete="off"></p>

          <p>パスワード:<input type="password" name="passedit" placeholder="パスワード" autocomplete="off" ></p>

          <p><input type="submit" name="edit" value="編集"></p>
</form>

<?php
//テーブルの表示
$sql = 'SELECT * FROM mission5 ORDER BY id';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
     //$rowの中にはテーブルのカラム名が入る
     echo $row['id'].'  ';
     echo $row['name'].'  ';
     echo $row['comment'].'  ';
     echo $row['dt'].'<br>';
     echo "<hr>";
}


?>

</body>
</html>
