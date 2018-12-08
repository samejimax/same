<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>アカウントをつくるファイル</title>
</head>
<body>
	<h2>アカウント作成</h2>

	<form action="" method="post">
		<p><label>メールアドレス</label>
				 <input type="email" name="email" id="email"></p>
				  <div id="info"><!--ここにajaxの戻り値が来る--></div>

		<p><label>パスワード</label>
				 <input type='password' id='password' name="password"></p>
	 	<p><label>パスワード確認</label>
 		 		<input type='password' name="password_conf"></p>

		<input type="button" id="insert" value="確認へ">
		<div id ="regist"></div>
	<input type="hidden" name="gazo" id="gazo" value="">
	</form>

	<img id="image_file" alt="アップした画像" style="display: none;">

	<hr>
	<form method="post" id="imgform" action="" enctype="multipart/form-data">
	ファイル:<input type="file" name="up_file"><br>
	<input type="button" id="imageup" value="upload">
	</form>

 <script src="https://code.jquery.com/jquery-2.2.4.js"></script>

 <script>
  $('#imageup').click(function() {
  	//画像送信して映すまでのajax通信を書く
  	var updir = '/php';
  	var formdata = new FormData($('#imgform').get(0));
    $.ajax({
      //↓ユーザーのPCから参照するので完全なURLが必要 【Aajx のSame Origin Policy】
        url: "http://localhost" + updir + "/img_up.php",
        type: "post",       //method
        processData:false,
        contentType:false,
        dataType: "html",   //送信データの種類 html json とか
        data:formdata
    })
    .done(function (response) {
    	//通信が成功した場合 php からの戻り値が response に入る
        $("#image_file").attr('src',updir + "/" + response).show();
        $('#gazo').val(response);
    })
    .fail(function (xhr,textStatus,errorThrown) {
    	//通信が失敗した場合
        alert('error');
    });
  });

$('#email').change(function () {
  $.ajax({
  	// ユーザーのPCから参照するので完全なURLが必要
    url: "http://localhost/php/param.php",
    type: "post",   //method
    dataType: "text",  // 送信データの種類 ,html ,json とか
    data:{'email':$('#email').val()}  //送るデータ 複数なら,で区切る

  }).done(function (response) {
  	// 通信が成功した場合 php からの戻り値がreoponseにはいる
    $("div#info").html(response);
  }).fail(function (xhr,textStatus,errorThrown) {
      //通信が失敗した場合
      alert('error');
  });
});  

 	//ボタンを押したタイミングで双方の一致を確認する
 	$('#insert').click(function(){
 		var pswd = $('[name="password"]').val();
 		var pswdcf = $('[name="password_conf"]');
 		if(pswd == pswdcf.val()){
 			pswdcf.next().html();
		
		$.ajax({
    url: "http://localhost/php/param.php",
    type: "post", 
    dataType: "text", 
    data:{'email':$('#email').val(),'password':$('#password').val(),'gazo':$('#gazo').val()}

  }).done(function (response) {
    $("#regist").html(response);
  }).fail(function (xhr,textStatus,errorThrown) {
      alert('error');
	});
 			return true;

 		}else{
 			pswdcf.after('<em>一致しません</em>');
 			pswdcf.next().css('color','red');
 			return false;
 		}
 	});
 </script>


</body>
</html>