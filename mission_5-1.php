<!DOCTYPE html>
<html lang="ja">
<html>
<head>
        <meta charset="UTF-8">
        <title>mission_5-1.php</title>
</head>
<body>

<?php
    $dsn ='データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	$sql = "CREATE TABLE IF NOT EXISTS log"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,"
        . "password char(30)"
        .");";

    $stmt = $pdo->query($sql);

    /*                      削除機能                        */

    //削除フォームの送信の有無で処理を分岐
    if (!empty($_POST['delnum']) && !empty($_POST['delpass'])) {
        
        //入力データの受け取りを変数に代入
        $delnum = $_POST['delnum'];
        $delpass = $_POST['delpass'];
        $id = $delnum;
        $sql = "SELECT * FROM log";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetchAll();
        foreach($result as $row) {

            //削除番号と行番号が一致しなければ書き込み
            if ($row['id'] == $delnum && $row['password'] == $delpass) {
                $sql = "DELETE FROM log WHERE id =:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id',$id,PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    /*                                    投稿機能                            */
    if (!empty($_POST['name']) && !empty($_POST['comment'])) {

        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $date = date('Y-m-d-H:i:s');
        $password = $_POST['password'];
        
        if(empty($_POST['editNO']) && !empty($_POST['password'])) {

            //書き込み処理
            $sql = $pdo->prepare("INSERT INTO log(name,comment,date,password) value (:name,:comment,:date,:password)");
            
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql -> execute();
            echo"書き込み完了";

        }                  
    }
        /*                      編集選択機能                     */

        //編集フォームの送信の有無で処理を分岐
        if (!empty($_POST['editnum']) && !empty($_POST['editpass'])) {

            //入力データの受け取りを変数に代入
            $editnum = $_POST['editnum'];
            $editpass = $_POST['editpass'];

            $sql = "SELECT * FROM log";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll();
            foreach($result as $row) {

                //投稿番号と編集対象番号が一致したらその投稿の「名前」と「コメント」を取得
                if ($row['id'] == $editnum && $row['password'] == $editpass) {


                    $editnumber = $row['id'];
                    $editname = $row['name'];
                    $editcomment = $row['comment'];
                    $editpass = $row['password'];

                }
            }
        }   
        
        /*                                 編集機能                         */
        if(!empty($_POST['editNO']) && !empty($_POST['password'])) {
            //入力データの受け取りを変数に代入
            $editNO = $_POST['editNO'];
            $name = $_POST['name'];
            $comment = $_POST['comment'];
            $id = $editNO;

            $sql = "SELECT * FROM log";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll();
            foreach($result as $row) {

                if($row['id'] == $editNO && $row['password'] == $password) {
                    $sql = "UPDATE log SET name=:name,comment=:comment where id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':name',$name,PDO::PARAM_STR);
                    $stmt->bindValue(':comment',$comment,PDO::PARAM_STR);
                    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();

                }
            }
        }
        ?>

    <form action="" method="post">
    名前:<br>
      <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname)) {
          echo $editname;} ?>"><br>
    コメント: <br>
        <textarea name = "comment" cols = "30" rows = "5" ><?php if(isset($editcomment)){
        echo$editcomment;}?></textarea>
        <br>
    パスワード:<br>
      <input type="password" name="password" placeholder="パスワード">
      <input type="hidden" name="editNO" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
      <br>
      <input type="submit" name="submit" value="送信"><br>
      <br>
    削除対象番号:
      <input type="text" name="delnum" placeholder="削除対象番号"><br>
    パスワード:
      <input type="password" name="delpass" placeholder="パスワード"><br>
      <input type="submit" value="削除"><br>
      <br>
    編集対象番号:
      <input type="text" name="editnum" placeholder="編集対象番号"><br>
    パスワード:
      <input type="password" name="editpass" placeholder="パスワード"><br>
      <input type="submit" value="編集">
    </form>

<hr>
＜投稿一覧＞<br>
    <?php
       $sql = "SELECT * FROM log";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){ //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    ?>
</body>
</html>