<?php
session_start();
include 'functions.php'
?>

<?=template_Header('login')?>
    <form class=loginForm action="login.php" method="get">
        <label for = "userID">User ID</label>
        <input type = "text" id = "userID" name = "userID"><br>
        <label for = "password">Password</label>
        <input type = "text" id = "password" name = "password"><br>

        <input class= LSubmit type = "submit" value = "Submit" name = "info">
    </form>
    <?php
    if(isset($_SESSION["username"])){
        header('Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/groups.php');
        exit();
    }elseif(isset($_GET['info'])){
        $userID=$_GET['userID'];
        $password=$_GET['password'];

        $db = pdo_connect_sqlite();
        $stmt = $db->prepare("SELECT * FROM user natural join groups where userID = :userID and password = :password");
    
    
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($results) == 0){
            //Bad data
        }else{
            $_SESSION['username'] = $results[0]['Name'];
            $_SESSION['userID'] = $userID;
            $_SESSION['password'] = $password;  
            $_SESSION['GID'] = $results[0]['GID'];
            $_SESSION['Gname'] = $results[0]['Gname'];
            header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/groups.php");
            exit();
        }

        //disconnect from db
        $db = null;

    }
    ?>
<?=template_Footer()?>