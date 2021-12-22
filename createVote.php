 <?php
    include 'functions.php';
    session_start();
    if(!isset($_SESSION['GID'])){
        header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/login.php");
        exit();
    }
?>

<?=template_Header('Create a Vote')?>
    <script>
        function add_row (){
            let candidateTable = document.querySelector('#candidate_table');
            let newRowNumId = candidateTable.rows.length;
            newRowNumId++;
            let newCandRow = document.createElement('tr');
            newCandRow.id = 'candidates' + newRowNumId;
            console.log('newCandRow.id: ' + newCandRow.id);
            let newCandData = document.createElement('td');
            let label = document.createElement('label');
            label.innerHTML = 'Candidate(s): ';
            newCandData.appendChild(label);
            let newCandInput = document.createElement('input');
            newCandInput.type = 'text';
            newCandInput.name = 'candidates[]';
            newCandInput.placeholder = 'Enter Candidate';
            newCandData.appendChild(newCandInput);
            newCandRow.appendChild(newCandData);
            candidateTable.appendChild(newCandRow);
        }

        function delete_row(){
            let candidateTable = document.querySelector('#candidate_table');
            let deleteRow = candidateTable.rows.length;
            let nodeName = '#candidates' + deleteRow;
            console.log(nodeName);
            let deletedNode = document.querySelector(nodeName);
            candidateTable.removeChild(deletedNode);
        }
    </script>

    <form method="get" action="createVote.php">
        <table id="candidate_table">
            <tr id = 'electionName'>
                <td>
                    <label class=createVoteLabels for = 'nameOfElection'>Election Name: </label>
                    <input class=ElectionNameInput type = 'text' name = 'nameOfElection' placeholder="Election Name">
                </td>
            </tr>
            <tr id = 'electionType'>
                <td>
                    <label class=createVoteLabels for='typeOfElection'>Type of Election: </label>
                    <select name ='typeOfElection'>
                        <option value = 'FPTP'>First Past the Post</option>
                        <option value = 'Plurality'>Plurality</option>
                    </select>
                </td>
            </tr>
            <tr id="candidates">
                <td>
                    <label class=createVoteLabels for = 'candidates'>Candidate(s): </label>
                    <input type="text" name="candidates[]" placeholder="Enter Candidate">
                </td>
            </tr>
        </table>

        <input class=createVoteButtons type="button" onclick="add_row();" value="ADD CANDIDATE">
        <input class=createVoteButtons type="button" onclick="delete_row();" value="DELETE CANDIDATE">
        <input class=createVoteButtons type="submit" name="submit_row" value="SUBMIT">
    </form>

<?php
    $db = pdo_connect_sqlite();
    //get the global variable for the group id
    //take the name of the election from the form
    //take the type of the election from the form
    //create a prepared insert statement
        //bind the parameters of ename, etype, gid
    //execute the prepared statement
    if (isset($_GET['submit_row'])){
        $stmt = $db->prepare("select count(*) as pollCount from election");
        $stmt->execute();
        $pollCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("select max(EID) as largest from election");
        $stmt->execute();
        $EIDMax = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($pollCount[0]['pollCount'] == 0){
            $EID = 1;
        }else{
            $EID = $EIDMax[0]['largest'] + 1;
        }
        $Ongoing = 1;
        $Ename = $_GET['nameOfElection'];
        $Type = $_GET['typeOfElection'];
        $candidatesArr = $_GET['candidates'];
            
        $electionSTMT = $db->prepare("insert into Election values (:Ongoing, :EID, :Ename, :GID, :Type)");
        $electionSTMT->bindParam(':Ongoing', $Ongoing, PDO::PARAM_INT);
        $electionSTMT->bindParam(':EID', $EID, PDO::PARAM_INT);
        $electionSTMT->bindParam(':Ename', $Ename, PDO::PARAM_STR);
        $electionSTMT->bindParam(':GID', $_SESSION['GID'], PDO::PARAM_INT);
        $electionSTMT->bindParam(':Type', $Type, PDO::PARAM_STR);
        $electionSTMT->execute();

        //reuse the value of the eID from the newly created election tuple
        //is it possible to loop through an html form with php?
        //if not then might have to create variables with js
        $stmt = $db->prepare("select count(*) as candCount from candidate");//two sqlite statements to create new CandID
        $stmt->execute();
        $candCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("select max(CandID) as largest from candidate");
        $stmt->execute();
        $candMax = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($candCount[0]['candCount'] == 0){
            $CandID = 1;
        }else{
            $CandID = $candMax[0]['largest'] + 1;
        }




        for ($counter = 0; $counter < count($candidatesArr); $counter++){
            $CandID = $CandID +1; //TODO: Bennett change this to the CandID generator
            $CandName = $candidatesArr[$counter];
            $candidateSTMT = $db->prepare("insert into Candidate values (:CandID, :CandName, :EID)");
            $candidateSTMT->bindParam(':CandID', $CandID, PDO::PARAM_INT);
            $candidateSTMT->bindParam(':CandName', $CandName, PDO::PARAM_STR);
            $candidateSTMT->bindParam(':EID', $EID, PDO::PARAM_INT);
            $candidateSTMT->execute();
        }
        $db = null;
        header("Location: http://129.114.19.78/~dbteam/DatabaseProject/pollingApp/groups.php");
    }
?>

<?=template_Footer()?>
