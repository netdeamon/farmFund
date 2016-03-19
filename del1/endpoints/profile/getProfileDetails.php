<?php
$app->post ( "/getProfileDetails", function () use($app) {


/*get the userid*/
$userid=$app->request()->post('userid');

//$userid = $_POST['userid'];

/*CONNECT TO THE MYSQL SERVER*/
$username="root";
$password="";
$conn = new PDO('mysql:host=localhost;dbname=discu6zu_discuss-book', $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    

/**/
$sql = $conn->prepare("SELECT USERID, USERNAME,FIRSTNAME,LASTNAME,USERNICKNAME,USEREMAIL,USERLEVEL,USERDATE,USERACTIVE FROM USERS WHERE USERID = :USERID");
$sql->bindParam('USERID', $userid, PDO::PARAM_INT);
$sql->execute();

$result = array();
$i=0;
while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {
	$result[$i] = array();
	$result[$i]['USERID']=$row['USERID'];
	$result[$i]['USERNAME']=$row['USERNAME'];
	$result[$i]['FIRSTNAME']=$row['FIRSTNAME'];
	$result[$i]['LASTNAME']=$row['LASTNAME'];
	$result[$i]['USERNICKNAME']=$row['USERNICKNAME'];
	$result[$i]['USEREMAIL']=$row['USEREMAIL'];
	$result[$i]['USERLEVEL']=$row['USERLEVEL'];
	$result[$i]['USERDATE']=$row['USERDATE'];
	$result[$i]['USERACTIVE']=$row['USERACTIVE'];
	$i++;
}
    
$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8020' );
echo json_encode ( $result );


});
?>