<html>
	<head>
	<meta charset = "utf-8">
	</head>
	<body>

<?php 
//DBに接続
$user ="ユーザー名";//
$pass ="パスワード";
$dsn = 'データベース名';
	$pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE=> PDO::ERRMODE_WARNING));
?>


<?php
//編集機能フォーム
	if(!empty($_POST["edit"])){
		if(!empty($_POST["edit_pass"])){
			$edit_pass = $_POST["edit_pass"];
			$edit = $_POST["edit"]; //変更する投稿番号

      try{
        $sql = 'SELECT * FROM mission_a WHERE id=:edit';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':edit', $edit, PDO::PARAM_INT);
		$stmt->execute();
        $edit_line = $stmt->fetch(PDO::FETCH_ASSOC);
		$check_pass = $stmt->fetch(PDO::FETCH_ASSOC);
		
        /*デバッグ用　完成したら消す
        echo "-----編集機能フォームデバッグ-----<br/>";
        var_dump($edit_line);
        echo "------------------------------<br/>";
        */

        if($edit_line['passwd'] == $edit_pass){ //パスワード確認
          $edit_num = $edit_line['id'];//番号を変数へ
          $edit_name = $edit_line['name'];//名前を変数へ
          $edit_com = $edit_line['comment'];//テキストを変数へ
        }else{
            echo "パスワードが一致しないため編集できません<br/>";
        }
      }catch(Exception $e){

        //デバッグ
        echo("エラー発生@編集機能フォーム:".$e->getMessage());
      }
    }else{
      echo "パスワードを入力してください<br/>";
    }
  }
?>


		<h1>掲示板</h1>
	<form action = "mission_5-1.php" method = "post">
		<p>
			<label for = "name">名前:</label>
			<input type="text" name="name" value="<?php if(!empty($edit_name)){echo $edit_name;}?>">
		</p>
		<p>
			<label for = "comment">コメント:</label>
			 <input type="text" name="comment" value="<?php if(!empty($edit_com)){echo $edit_com;}?>">
			 <input type="hidden" name="e_judge" value="<?php if(!empty($edit)){echo $edit;}?>">
		</p>
		<p>
			<label for="password">Password:</label>
			<input type="text" name="pass">
		</p>
		<p>
			<input type = "submit" value = "送信">
		</p>
	</form>
	<form action = "mission_5-1.php" method = "post">
		<p>
			<label for = "delete">削除番号:</label>
			<input type = "number" name = "delete">
		</p>
		<p>
			<label for="password">Password:</label>
			<input type="text" name="del_pass">
		</p>
		<p>
			<input type = "submit" value = "削除">
		</p>
	</form>
	<form action = "mission_5-1.php" method = "post">
		<p>
			<label for = "edit">編集対象番号:</label>
			<input type = "text" name = "edit" >
		<p/>
		<p>
			<label for="password">Password:</label>
			<input type="text" name="edit_pass">
		</p>
		<p>
			<input type = "submit" value = "編集">
		</p>
		</form>
		<hr/>
	</body>
</html>


<?php //新規投稿処理
if(isset($_POST["comment"]) && empty($_POST["e_judge"])){
	if(!empty($_POST["pass"])){
		
		$name = $_POST["name"];
		$comment = $_POST["comment"]; 
		$time = date('Y/m/d/ H:i:s');
		$pass = $_POST["pass"];
		
		try{
		$sql = $pdo -> prepare('INSERT INTO mission_a (name, comment, time, passwd) VALUES (:name, :comment, :time, :passwd)');
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':time', $time, PDO::PARAM_STR);
		$sql -> bindParam(':passwd', $pass, PDO::PARAM_STR);
		$sql -> execute();
		}catch(Exception $e){
        //デバッグ
        echo("エラー発生@新規投稿処理:".$e->getMessage());
      }

    }else{
      echo "パスワードを設定して投稿し直してください<br/>";
    }
  }
?>
<?php
 //削除処理
	if(isset($_POST["delete"]) === true){
		if(!empty($_POST["del_pass"])){

	$del_pass = $_POST["del_pass"];
	$delete = $_POST["delete"];
	try{
		$sql = 'SELECT passwd FROM mission_a where id=:del_id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':del_id', $delete, PDO::PARAM_INT);
		$stmt->execute();
		$check_pass = $stmt->fetch(PDO::FETCH_ASSOC);
        //パスワードが一致すれば削除
        if($del_pass == $check_pass["passwd"]){
            $sql = 'DELETE FROM mission_a WHERE id=:del_id';
            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(':del_id', $delete, PDO::PARAM_INT);
            $stmt->execute();
             }else{  //パスワード不一致の場合
            echo "パスワードが一致しないため削除できません<br/>";
          }
        }catch(Exception $e){
          //デバッグ
          echo "エラー発生@削除処理:".$e->getMessage();
        }
    }else{
      echo "パスワードを入力してください<br/>";
    }
  }

 //編集機能始まり
if(!empty($_POST["e_judge"])){  //編集作業であることを判定し、ファイルに再書き込み

	$num = $_POST["e_judge"]; //変更する投稿番号
	$new_name = $_POST["name"];
	$new_comment =$_POST["comment"]; 
	$new_pass = $_POST["pass"];
	$time = date('Y/m/d/ H:i:s');
	
	try{
	$sql = 'UPDATE mission_a SET name=:new_name, comment=:new_comment, time=:new_time, passwd=:new_pass WHERE id=:num';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':new_name', $new_name, PDO::PARAM_STR);
	$stmt->bindParam(':new_comment', $new_comment, PDO::PARAM_STR);
	$stmt->bindParam(':new_time', $time, PDO::PARAM_STR);
	$stmt->bindParam(':num', $num, PDO::PARAM_STR);
	$stmt->bindParam(':new_pass', $new_pass, PDO::PARAM_STR);
	$stmt->execute();
	}catch(Exception $e){
      //デバッグ
      echo "エラー発生@編集反映処理:".$e->getMessage();
    }
}


 //表示処理
	//野崎追記...動作上問題はなさそうだけど今後のために表示処理は最後においた方が良いかも。

	$sql = 'SELECT * FROM mission_a';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['time'].'<br>';
	echo "<hr>";
	}
?>