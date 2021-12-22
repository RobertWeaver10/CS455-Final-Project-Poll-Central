<?php
include 'functions.php';
session_start();
if(isset($_GET['logout'])){
session_destroy();
header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/home.php");
}
?>
<!DOCTYPE html>
<?=template_Header('Home Page')?>
<html>
    <div class=HomeDiv> 
        <h1 class=homeTitle> Welcome to Poll Central! </h1>
        <br />
        <p class=homeDesc> Here you can create polls for your group to vote on!  </p>
        <br/>
        <h2> Navigation </h1>
        <br/>
        <p> This is the home page; On the top right you will see several links to help you navigate the website. </p>
        <p> Group Polls will take you to a list of polls your current group has created. </p> 
        <p> The Login function will log you into your acount and group, if you are curently logged in it will change to the logout button. </p> 
        <p> The Create vote function allows you to create a new poll for your current group. </p>   
        <p> The Create Group function will allow you to create a new group. </p>
        <p> Once all people in the group have voted the votes are tallied and the winner declared. </p>
    </div>
    

    
    <?=template_Footer()?>

</html>
    
