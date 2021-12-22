<?php
    session_start();
    include 'functions.php';
    template_Header("Group Elections");

    if(!isset($_SESSION['GID'])){
        header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/login.php");
        exit();
    }


    $db = pdo_connect_sqlite();
    $stmt = $db->prepare("SELECT * FROM groups natural join election where GID = :GID");
    $stmt->bindParam(':GID', $_SESSION['GID'], PDO::PARAM_INT);
    $stmt->execute();
    echo "<table class='groups'> 
                    <thead> 
                        <tr> 
                            <th> Election Name </th> 
                            <th> Type </th>
                            <th> ID </th>
                            
                        </tr>
                    </thead>
                        <tbody>";
    foreach($stmt as $tuple) {
            $link = "http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/poll.php?EID=".$tuple['EID'];
            echo " 
                        <tr>
                            <td> <a href=$link > $tuple[Ename] </a> </td>
                            <td> $tuple[Type] </td>
                            <td> $tuple[EID] </td>
                            
                        </tr>
                    
                ";
            //echo "<a href=$link > $tuple[Ename] </a><br/>\n";
    }
echo "  </tbody>
    </table>";



    $stmt = $db->prepare("SELECT * FROM groups natural join user where GID = :GID");
    $stmt->bindParam(':GID', $_SESSION['GID'], PDO::PARAM_INT);
    $stmt->execute();
    echo "<h2 class=userTitle > Group Users </h2>";
    foreach($stmt as $tuple) {
        echo "<font class=users color='blue'>$tuple[Name]</font>, ID: $tuple[UserID] <br/>\n";
    }
    
    //disconnect from db
    $db = null;
?>
<?=template_Footer()?>