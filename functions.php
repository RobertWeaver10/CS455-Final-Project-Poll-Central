<?php
function pdo_connect_sqlite(){
    $db_file = './DB/voteDatabase.db';
    try{
        return new PDO('sqlite:'.$db_file);
    }catch(PDOException $exception){
        exit('Failed to connect to database.');
    }
}

function template_Header($title){
echo <<<EOT
<!Doctype HTML>
<html>
    <head>
        <meta charset = "utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>$title</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href = "style.css" rel = "stylesheet" type = "text/css">
    <head>
    <body>
        <header class = "headerTemplate">
            <h1 id = "pageName">$title</h1>
            <nav class = "navtop">
                <ul class = "navtop-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="groups.php">Group Polls</a></li>
EOT;
template_Mid();
echo <<<EOT
                    <li><a href="createVote.php">Create Vote</a></li>
                    <li><a href="createGroup.php">Create Group</a></li>
                </ul>
            </nav>
        </header>
EOT;
}


function template_Mid(){
if(isset($_SESSION['username'])){
echo <<<EOT
                    <li><a href="home.php?logout=1">Logout</a></li>
EOT;
}else{
echo <<<EOT
                    <li><a href="login.php">Login</a></li>
EOT;
}
}

function template_Footer(){
echo <<<EOT
    </body>
<html>
EOT;
}

function displayPoll($EID) { # Input EID
echo <<<EOT
<!DOCTYPE HTML>
<html>
<div>
            
</div>
EOT;
}



?>
