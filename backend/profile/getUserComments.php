<?php
$app->post ( "/getUserComments", function () use($app) {


/*get the userid*/
$userid=$app->request()->post('userid');

//$userid = $_POST['userid'];

/*CONNECT TO THE MYSQL SERVER*/
$username="root";
$password="";
$conn = new PDO('mysql:host=localhost;dbname=discu6zu_discuss-book', $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    

/**/
	$sql1 = $conn->prepare("SELECT QC.QUESTIONID,QC.COMMENTID,QC.COMMENT,QC.COMMENTTIME,QC.USERID,QC.USERNAME FROM QUESTIONS_COMMENTS AS QC WHERE QC.USERID = :USERID");
	$sql1->bindParam('USERID',$userid,PDO::PARAM_INT);
	$sql1->execute();

	$j=0;
	$result['QUESTIONS'] = array();
	while($row1 = $sql1->fetch(PDO::FETCH_ASSOC)){
		$result['QUESTIONS']['COMMENTS']=array();
		$result['QUESTIONS']['COMMENTS'][$j]=array();
		$result['QUESTIONS']['COMMENTS'][$j]['QUESTIONID']=$row1['QUESTIONID'];
		$result['QUESTIONS']['COMMENTS'][$j]['COMMENTID']=$row1['COMMENTID'];
		$result['QUESTIONS']['COMMENTS'][$j]['COMMENT']=$row1['COMMENT'];
		$result['QUESTIONS']['COMMENTS'][$j]['COMMENTTIME']=$row1['COMMENTTIME'];
		$result['QUESTIONS']['COMMENTS'][$j]['USERID']=$row1['USERID'];
		$result['QUESTIONS']['COMMENTS'][$j]['USERNAME']=$row1['USERNAME'];
		$j++;
	}

	$sql2 = $conn->prepare("SELECT AC.ANSWERID,AC.COMMENTID,AC.COMMENT,AC.COMMENTTIME,AC.USERID,AC.USERNAME FROM ANSWERS_COMMENTS AS AC WHERE AC.USERID = :USERID");
	$sql2->bindParam('USERID',$userid,PDO::PARAM_INT);
	$sql2->execute();

	$j=0;
	$result['ANSWERS']=array();
	while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
		$result['ANSWERS']['COMMENTS']=array();
		$result['ANSWERS']['COMMENTS'][$j]=array();
		$result['ANSWERS']['COMMENTS'][$j]['ANSWERID']=$row2['ANSWERID'];
		$result['ANSWERS']['COMMENTS'][$j]['COMMENTID']=$row2['COMMENTID'];
		$result['ANSWERS']['COMMENTS'][$j]['COMMENT']=$row2['COMMENT'];
		$result['ANSWERS']['COMMENTS'][$j]['COMMENTTIME']=$row2['COMMENTTIME'];
		$result['ANSWERS']['COMMENTS'][$j]['USERID']=$row2['USERID'];
		$result['ANSWERS']['COMMENTS'][$j]['USERNAME']=$row2['USERNAME'];
		$j++;
	}

	$sql3 = $conn->prepare("SELECT N.BOOKTIPID,N.COMMENTID,N.COMMENT,N.TIME,N.USERID,N.USERNAME FROM BOOKTIP_COMMENTS AS N WHERE N.USERID = :USERID");
	$sql3->bindParam('USERID',$userid,PDO::PARAM_INT);
	$sql3->execute();

	$j=0;
	$result['NOTES']=array();
	while($row3 = $sql3->fetch(PDO::FETCH_ASSOC)){
		$result['NOTES']['COMMENTS']=array();
		$result['NOTES']['COMMENTS'][$j]=array();
		$result['NOTES']['COMMENTS'][$j]['BOOKTIPID']=$row3['BOOKTIPID'];
		$result['NOTES']['COMMENTS'][$j]['COMMENTID']=$row3['COMMENTID'];
		$result['NOTES']['COMMENTS'][$j]['COMMENT']=$row3['COMMENT'];
		$result['NOTES']['COMMENTS'][$j]['COMMENTTIME']=$row3['TIME'];
		$result['NOTES']['COMMENTS'][$j]['USERID']=$row3['USERID'];
		$result['NOTES']['COMMENTS'][$j]['USERNAME']=$row3['USERNAME'];
		$j++;
	}

$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8020' );
echo json_encode ( $result );


});
?>