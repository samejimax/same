<?php header("Content-type: text/html; charset=utf-8");
  // 通信ヘッダは文字を書き出す前に実行！
  $prio=0;
   if(isset($_POST['priority'])){
   	// post値は文字列型なので数値型に変更している カラなら0になる
   	$prio=(int)$_POST['priority'];
   }
  require_once('connect.php');
  require_once('mojifilter.php');
  $dbh=dbconnect();
  //追加ボタンが押されたときの処理
  if(!empty($_POST{'insert'}) && !empty($_POST['todocont'])){
  	if($prio === 0) $prio=1; //すべてを選んだら強制的に低にする
  	$sql = 'INSERT INTO todolist(todo, prio, created)
  					VALUES(?, ?, CURDATE())';
  	$sth=$dbh->prepare($sql);
  	$sth->bindValue(1, $_POST['todocont'],PDO::PARAM_STR);
  	$sth->bindValue(2, $prio,PDO::PARAM_INT);
  	$sth->execute();
  // 検索ボタンが押されたときの処理
  }else if(!empty($_POST['search'])){
  	// $_POST['prio']は2なら"すべて"にしたい
  	 $ichiran = search();

  }else if(!empty($_POST['delete']) && !empty($_POST['checktodo'])) {
  	//削除ボタンが押されたときの処理
  	$checkOn = $_POST['checktodo'];
  	$ids = '';
  	 foreach ($checkOn as $id) {
  	 	$ids .= $id . ",";
  	 }
  	 $ids = rtrim($ids, ","); //,を取り除く
  	  $sql="DELETE FROM todolist
  				  WHERE id
  				  IN($ids)";
  	  $sth=$dbh->prepare($sql);
  	  $sth->execute();
  	  echo $ids, "を削除しました";
  	  $ichiran = search();
  }
// $obj=$sth->fetch(PDO::FETCH_OBJ);
//	echo $obj->todo   ;   先頭行しか取り出せない 
//オブジェクト型で取得した場合は フィールド名を -> アロー演算子で書く

// $row = $sth->fetch(PDO::FETCH_NUM);
// 	echo $row[1];    先頭行しか取り出せない 

//$row =	$sth->fetchAll();  Allで変換 → 2次元配列
//	 	echo $row[1]['todo'];

// 特定のフィールドを指定して 使う場合
//	print_r($sth->fetchAll(PDO::FETCH_COLUMN, 1));
  
?>

<form action="" method="post">
	<p>やること入力<br>
		<textarea name="todocont"></textarea>

		<select name="priority">
			<option value="0">すべて</option>
			<option value="1">低</option>
			<option value="2">高</option>
		</select>
	</p>
	<input type="submit" name="insert" value="追加">
	<input type="submit" name="search" value="検索">
	<input type="submit" name="delete" value="削除">
<?php echo @$ichiran; ?>
</form>

<?php
function search(){
   global $dbh;	
	$sql = "SELECT * FROM todolist";
	 if($_POST['priority'] !=0)
	 	$sql .= " WHERE prio = ?";

	$sth=$dbh->prepare($sql);
	 if($_POST['priority'] !=0)
	  $sth->bindValue(1, $_POST['todocont'],PDO::PARAM_INT);
	$sth->execute(); //MySQLから持ってきたデータ

	$ichiran = "";
	foreach ($sth as $key => $row) {
		$ichiran .= "
		<br><input type='checkbox' name='checktodo[]' value='"
		 . h($row["id"]) . "'>"
		 . h($row['todo']);
	}
	return $ichiran;
}
?>