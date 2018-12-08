<?php
  session_start();
  $sid = session_id();

	if ( !empty($_POST['password']) && !empty($_POST['email']) ){

		//emailでuserテーブルを検索
		 require_once("mojifilter.php");
		 require_once("connect.php");
			 $dbh = dbconnect();

       //ヒットした行数をカウント
			 $email = $_POST['email'];
			 $password = h($_POST['password']);
			 $zerofrag = false; // counterが0に戻れば

			 $sql="SELECT code, email, password, timestump, counter FROM users WHERE email = ? ";
		   $stmt = $dbh->prepare($sql);
		   $stmt->bindValue(1, $email, PDO::PARAM_STR);
		   $stmt->execute();

				$rowcount = $stmt->rowCount(); // ヒットした行数を数える
			 if($rowcount){
			 	$row = $stmt->fetch(PDO::FETCH_ASSOC);
			 	
			 	// 1件あったら パスワードを入力値とDBの値で照合
			 	if($row['timestump']!==0 	&&
			 		time() - $row['timestump'] > (60*30)){
			 		// counterを0に戻す
			 		if($row['counter'] != 0){ //今の値が0じゃない
			 			$sql="UPDATE users SET counter = 0 WHERE email = ?";
			 			$stmt = $dbh->prepare($sql);
			 			$stmt->bindValue(1, $email , PDO::PARAM_STR);
			 			$res = $stmt->execute();
			 			$zerofrag = true;
			 		}
			 		//ここからパスワード照合
				 		if( pv(1) ){
				 			$_SESSION['code']=$row['code'];
							 exit; // 認証成功ならSTOP
						 } else{  // 追加 
							if(!$zerofrag && $row['counter'] >= 3){
								updateTime();
								}
						 } //追加ここまで

			 	}else{  //30分経っていないなら
			 		//失敗回数が < 3
			 		if($row['counter'] < 3){
			 		//ここからパスワード照合			
			 		if(pv(2)){
			 			$_SESSION['code']=$row['code'];
			 			exit;
			 		}
			 		}else{
			 			echo "只今ログインできません";
			 			// 失敗回数が >=3
			 			updateTime();
		 		  }
			 	}// 30分たってないからEND

			 	}else{
		   		echo "メールアドレスかパスワードが違います";
		   	}
    }
       // パスワードを入力値とDBの値で照合
			 	function pv($t){
			 		// 外側の変数を関数内で使うための宣言
			 		global $password; global $row; global $dbh; global $email; global $zerofrag;
			 		if(password_verify($password, $row['password'])){
			 			echo "認証成功";
			 			$sql="UPDATE users SET counter = 0 WHERE email = ?";
			 			$stmt = $dbh->prepare($sql);
			 			$stmt->bindValue(1, $email , PDO::PARAM_STR);
			 			$stmt->execute();
			 			//記事投稿画面へリダイレクト
			 			header('Location: ./dashboard.php');
			 			return true;
			 		}else{
			 			$addcount = $zerofrag ? 1 : ++$row['counter'] ;
			 			$sql="UPDATE users SET counter =". $addcount . " WHERE email = ?";
			 			$stmt = $dbh->prepare($sql);
			 			$stmt->bindValue(1, $email , PDO::PARAM_STR);
			 			$stmt->execute();
			 			echo "認証失敗" .$t;
			 			var_dump( $addcount ,$zerofrag );
			 			return false;
			 		}
			 	}

			 	function updateTime(){
			 		global $dbh; global $email;
					$sql="UPDATE users SET timestump = ". time() . " WHERE email = ?";
					$stmt = $dbh->prepare($sql);
					$stmt->bindValue(1, $email , PDO::PARAM_STR);
					$stmt->execute();
			 	}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>ログインするファイル</title>
</head>
<body>
	<h2>ユーザーログイン</h2>
	<form action="" method="post">
		<input type="hidden" name="himitsu" value="<?=$sid?>">
		<p><label>メールアドレス</label>
				 <input type="email" name="email"></p>

		<p><label>パスワード</label>
				 <input type="password" name="password"></p>

		<input type="submit" name="確認へ">
	</form>
</body>
</html>