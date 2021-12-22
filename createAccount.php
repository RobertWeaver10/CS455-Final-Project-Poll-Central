<?php
include 'functions.php';
session_start();
?>
<?=template_Header('Create Account')?>
<form action="createAccount.php" method="get">
    <label for = "username">Username</label>
    <input type = "text" id = "username" name = "username"><br>
    <label for = "password">Password</label>
    <input type = "password" id = "password" name = "password"><br>
    <label for = "password">Re-enter Password</label>
    <input type = "password" id = "password" name = "samePassword"><br>
    <label for = "groupID">Group ID</label>
    <input type = "text" id = "groupID" name = "groupID"><br>
    <label for = "userID">Group Password</label>
    <input type = "password" id = "groupPW" name = "groupPW"><br>
    <input type = "submit" value = "Submit" name = "info">
</form>

<?php
    if(isset($_GET['info'])){
        $username=$_GET['username'];
        $password=$_GET['password'];
        $groupID=$_GET['groupID'];
        $groupPW=$_GET['groupPW'];
        if($password != $_GET['samePassword']){
            echo "Passwords do not match";
            exit();
        }
        $db = pdo_connect_sqlite();
        $stmt = $db->prepare("SELECT * FROM user natural join groups where GID = :groupID");
        $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT * FROM groups where GID = :groupID");
        $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!isset($groups[0])){
            echo "No such group";
            exit();
        }
        if($groups[0]['Gpassword'] != $groupPW){
            echo "Incorrect group password";
            exit();
        }
        foreach($results as $tuple){
            if($username == $tuple['Name']){
                echo "Unfortunately that username is taken by a member of your specified group";
                exit();
            }
        }


        $stmt = $db->prepare("select count(*) as numUser from user");//two sqlite statements to create $newUserID
        $stmt->execute();
        $numUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("select max(UserID) as largest from user");
        $stmt->execute();
        $userMax = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($numUsers[0]['numUser'] == 0){
            $newUserID = 1;
        }else{
            $newUserID = $userMax[0]['largest'] + 1;
        }
        $stmt = $db->prepare("insert into User values (:UserID, :Name, :Password, :GID)");
        $stmt->bindParam(':UserID', $newUserID, PDO::PARAM_INT);
        $stmt->bindParam(':Name', $username, PDO::PARAM_STR);
        $stmt->bindParam(':Password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':GID', $groupID, PDO::PARAM_INT);
        $stmt->execute();
        
        $db = null;
        
        $_SESSION['username'] = $username;//logging user into newly created account
        $_SESSION['userID'] = $newUserID;
        $_SESSION['password'] = $password;  
        $_SESSION['GID'] = $groupID;
        $_SESSION['Gname'] = $groups[0]['Gname'];
        header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/groups.php");

    }
    ?>
<?=template_Footer()?>