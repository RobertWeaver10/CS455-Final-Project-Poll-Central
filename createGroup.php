<?php
include 'functions.php';
session_start();
?>
<?=template_Header('Create Group')?>
<form class=createGroupForm action="createGroup.php" method="get">
    <label for = "Gname">Group Name</label>
    <input type = "text" id = "Gname" name = "Gname"><br>
    <label for = "Gpassword">Group Password</label>
    <input type = "password" id = "Gpassword" name = "Gpassword"><br>
    <label for = "Gpassword">Re-enter Password</label>
    <input type = "password" id = "Gpassword" name = "samePassword"><br>
    <input class=CGSubmit type = "submit" value = "Submit" name = "info">
</form>

<?php
    if(isset($_GET['info'])){
        $Gname=$_GET['Gname'];
        $Gpassword=$_GET['Gpassword'];
        $samePassword=$_GET['samePassword'];
        if($Gpassword != $_GET['samePassword']){
            echo "Passwords do not match";
            exit();
        }
        $db = pdo_connect_sqlite();
        $stmt = $db->prepare("SELECT Count(*) as num FROM groups");//two sqlite statements to create new GID
        $stmt->execute();
        $groupNum = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT Max(GID) as largest from groups");
        $stmt->execute();
        $maxGroup = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $GID = 1;
        if($groupNum[0]['num'] == 0){
            $GID = 0;
        }else{
            $GID = $maxGroup[0]['largest'] + 1;
        }
        $stmt = $db->prepare("insert into Groups values (:Gname, :GID, :Gpassword)");
        $stmt->bindParam(':Gname', $newUserID, PDO::PARAM_STR);
        $stmt->bindParam(':GID', $username, PDO::PARAM_INT);
        $stmt->bindParam(':Gpassword', $Gpassword, PDO::PARAM_STR);
        $stmt->execute();
        
        // $db = null;
        
        echo "Group Created! Group ID (Make sure to remember this!): $GID";
        $db = null;
    }
    ?>
<?=template_Footer()?>