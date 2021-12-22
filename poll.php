 <?php
    session_start();
    include 'functions.php';
    if(!isset($_SESSION['GID'])){
        header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/login.php");
        exit();
    }
    template_Header(`$results[0]['Ename']`);

    if(!isset($_GET['EID'])){
        header('Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/groups.php');
        exit();
    }

    $db = pdo_connect_sqlite();
    $stmt = $db->prepare("SELECT * FROM election where EID =:EID");
    $stmt->bindParam(':EID', $_GET['EID'], PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(sizeof($results) == 0 || $_SESSION['GID'] != $results[0]['GID']){
        header('Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/groups.php');
        exit();
    }
    //code that finds the whether the election is over.
    // $stmt = $db->prepare("SELECT Count(DISTINCT UserID) as numUsers FROM election natural join candidate where EID =:EID");
    // $stmt->bindParam(':EID', $_GET['EID'], PDO::PARAM_INT);//finding the number of users who have voted in the election
    // $stmt->execute();

    // $numUsersVoted = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['numUsers'];

    // $stmt = $db->prepare("SELECT Count(DISTINCT UserID) as numUsers from groups natural join user where GID =:GID");
    // $stmt->bindParam(':EID', $_SESSION['GID'], PDO::PARAM_INT);//finding the number of users in the group
    // $stmt->execute();
    // $numGroupUsers = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['numUsers'];

    $stmt = $db->prepare("SELECT count(*) as numVotes From vote natural join user natural join election where EID = :EID and UserID = :UserID");
    $stmt ->bindParam(':EID', $_GET['EID'], PDO::PARAM_INT);
    $stmt ->bindParam(':UserID', $_SESION['UserID'], PDO::PARAM_INT);//finds numVotes (number of votes the current user session has made in the election)
    $stmt->execute();
    if($stmt->fetchAll(PDO::FETCH_ASSOC)[0]['numVotes'] > 0){
        //return winner or leader.



        $stmt = $db->prepare("SELECT * from vote natural join election where EID = :EID");
        $stmt ->bindParam(':EID', $_GET['EID'], PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // exit();//probably
        $tally = array();
        foreach($results as $tuple){
            if(!isset($tally[$tuple['CandID']])){
                $tally[$tuple['CandID']] = $tuple['Choice'];
            }else{
                $tally[$tuple['CandID']] += $tuple['Choice'];//adding up values
            }
            $tally = asort($tally);
        }
    }elseif(!isset($_GET['Choice'])){
        $stmt = $db->prepare("SELECT CandID from vote natural join candidate where EID = :EID ORDER BY CandID");
        $stmt ->bindParam(':EID', $_GET['EID'], PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $candIDs = array();
        for($i = 0; $i < count($resuls); $i++){
            $candIDs[$i] = $results[$i]['CandID'];
        }
        


        $stmt = $db->prepare("SELECT CandName from vote natural join candidate where EID = :EID ORDER BY CandID");
        $stmt ->bindParam(':EID', $_GET['EID'], PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $candNames = array();
        for($i = 0; $i < count($results); $i++){
            $candNames[$i] = $results[$i]['CandID'];
        }
        





        //if the code is running in here then my html code should be displayed
        //if this is a fptp vote this is the appropriate html
        echo <<<EOT
        <div class = "voteFormWrapper" id = "voteFormWrapper">
            
            <form method = "get" action = "poll.php" id = "voterForm">
                <table>
                    <tr>
                        <td>
                            <select name = "voteFormDropdown" id = "voteForm">
                                <option value = "placeHolder">Select Candidate(s)</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <input type="submit" name = "voteFormSubmit" value="SUBMIT">
            </form>

            <table id = "candidateTable">
                <tr>
                    <td>Candidate Number</td>
                    <td>Candidate Name</td>
                </tr>
                
            </table>
        </div>
        
        EOT;

        //else it is a ranked vote and this is the propper html
        echo <<<EOT
        <div class = "voteFormWrapper" id = "voteFormWrapper">
            <form method = "get" action = "navbarTest.html" id = "voterForm">
                <table id = 'ballotFormTable'>
                    <tr>
                        <td>Candidate Rank</td>
                        <td>Candidate name</td>
                    </tr>
                </table>
                <input type="submit" name = "voteFormSubmit" value="SUBMIT">
            </form>
            <table id = "candidateTable">
                <tr>
                    <td>Candidate Number</td>
                    <td>Candidate Name</td>
                </tr>
            
            </table>
        </div>
        
        EOT;

        echo "<script type='text/javascript'>", //check whether the vote is fptp or ranked
        //if fptp
             "addCandVoterForm(json_encode($candNames), json_encode($candIDs))",
             "addCandToTable(json_encode($candNames))",
             "</script>";
                //else its a ranked poll so call createRankedBallot
    }else{
        echo "uh oh for now";
    }
    
    
    
    //disconnect from db
    $db = null;
?>
<?=template_Footer()?>

<script type = 'text/javascript'>
    function addCandVoterForm (candidateNameArr, candidateIDArr){ //all candidates with the same election id
    let voterForm = document.querySelector('#voteForm');
    console.log(voterForm);
    let candNameArr = candidateNameArr;
    let candIDArr = candidateIDArr;
    for (var i = 0; i < candNameArr.length; i++){
        var candOption = document.createElement("option");
        candOption.value = candIDArr[i];; //make CandID
        candOption.innerHTML = candNameArr[i];
        voterForm.appendChild(candOption);
    }
}

function addCandToTable (candidateNameArr){ //all the candidates with the same election id
    let candidateTable = document.querySelector('#candidateTable');
    console.log(candidateTable);
    let candNameArr = candidateNameArr;
    for (var i = 0; i < candNameArr.length; i++){
        let candidateRow = document.createElement("tr");
        let candidateNum = document.createElement("td");
        let candidateName = document.createElement("td");
        candidateRow.id = "candidateRow" + (i+1);
        candidateNum.innerHTML = i+1;
        candidateName.innerHTML = candNameArr[i];
        candidateRow.append(candidateNum);
        candidateRow.append(candidateName);
        candidateTable.append(candidateRow);
    }
}

function createRankedBallot(candidateNamesArr, candidateIDArr){
    let ballotFormTable = document.querySelector('#ballotFormTable');
    console.log(ballotFormTable);
    let candidateNames = candidateNamesArr;
    let candidateIDs = candidateIDArr;
    for (var i = 0; i < candidateNames.length; i++){
        let candidateRow = document.createElement('tr');
        let candidateRank = document.createElement('td');
        let candName = document.createElement('td');
        let candRankDropDown = document.createElement('select');
        candRankDropDown.name = "choices[]";
        for (var j = 0; j < candidateNames.length; j++){
            let specificRank = document.createElement('option');
            specificRank.value = (j+1);
            specificRank.innerHTML = j+1;
            candRankDropDown.appendChild(specificRank);
        }
        candidateRank.appendChild(candRankDropDown);
        candidateRow.appendChild(candidateRank);
        candName.innerHTML = candidateNames[i];
        candidateRow.appendChild(candName);
        ballotFormTable.appendChild(candidateRow);
    }

}

</script>