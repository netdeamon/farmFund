<?php
$app->post ( "/getReply", function () use($app) {

/*RETURNS ANSWER
THE QUESTION
THE BOOK DETAILS
ANSWER VOTES
*/
/*get the userid*/
$questionid=$app->request()->post('questionid');
$pageno=$app->request()->post('pageno');
$results_per_page=$app->request()->post('results_per_page');

if (! $pageno) {
	$pageno = 0;
} else {
	$pageno = intval ( abs ( $pageno ) );
	$pageno = (($pageno - 1) * 10);
}
if (! $results_per_page) {
	$results_per_page = 10;
} else {
	$results_per_page = intval ( abs ( $results_per_page ) );
}
if ($pageno < 0) {
	$pageno = 0;
}
if ($results_per_page <= 0) {
	$results_per_page = 1;
}

//$userid = $_POST['userid'];

/*CONNECT TO THE MYSQL SERVER*/
$username="root";
$password="";
$conn = new PDO('mysql:host=localhost;dbname=discu6zu_discuss-book', $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    

/**/
$sql = $conn->prepare("SELECT Q.QUESTIONID,Q.QUESTIONTITLE,Q.QUESTIONFROMPAGENO,Q.QUESTIONTOPAGENO,Q.QUESTIONREF,Q.QUESTIONTITLE,Q.QUESTION,Q.USERID as QUSERID,Q.USERNAME AS QUSERNAME,Q.BOOKID,Q.QUESTIONTIME,A.ANSWERID,A.ANSWER,A.USERID,A.USERNAME,A.ANSWERTIME,A.BOOKID,A.EDITTIME,B.BOOKID,B.BOOKNAME,B.BOOKAUTHOR,B.BOOKAUTHOR2,B.BOOKAUTHOR3,B.BOOKAUTHOR4,B.BOOKPUBLICATION,B.ISBN_10,B.ISBN_13 FROM QUESTIONS_ANSWERS AS QA INNER JOIN ANSWERS AS A INNER JOIN QUESTIONS AS Q INNER JOIN BOOKSDETAILS AS B ON QA.QUESTIONID = Q.QUESTIONID AND Q.QUESTIONID = :QUESTIONID AND QA.ANSWERID = A.ANSWERID AND B.BOOKID=Q.BOOKID LIMIT :PAGENO , :RESULTS_PER_PAGE");
$sql->bindParam('QUESTIONID', $questionid, PDO::PARAM_INT);
$sql->bindParam('PAGENO', $pageno, PDO::PARAM_INT);
$sql->bindParam('RESULTS_PER_PAGE', $results_per_page, PDO::PARAM_INT);
$sql->execute();

$result = array();
$i=0;
$result['QUESTION'] = array();
$result['ANSWERS']=array();
while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {
		
		

	if($i==0){

		$result['QUESTION']['QUESTIONID']=$row['QUESTIONID'];
		$result['QUESTION']['QUESTIONFROMPAGENO']=$row['QUESTIONFROMPAGENO'];
		$result['QUESTION']['QUESTIONTOPAGENO']=$row['QUESTIONTOPAGENO'];
		$result['QUESTION']['QUESTIONREF']=$row['QUESTIONREF'];
		$result['QUESTION']['QUESTIONTITLE']=$row['QUESTIONTITLE'];
		$result['QUESTION']['QUESTION']=$row['QUESTION'];
		$result['QUESTION']['USERID']=$row['QUSERID'];
		$result['QUESTION']['USERNAME']=$row['QUSERNAME'];
		$result['QUESTION']['BOOKID']=$row['BOOKID'];
		$result['QUESTION']['QUESTIONTIME']=$row['QUESTIONTIME'];	
		$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
		$title=str_replace($chars, "-",$row['QUESTIONTITLE']);	
		$title=str_replace(" ", "-",$title);
		$title=str_replace("+", "-", $title);	
		$result['QUESTION']['QUESTIONLINK']=urlencode(base64_encode($row['QUESTIONID'])).'/'.$title;
	
	}
		$result['ANSWERS'][$i]=array();
	$result['ANSWERS'][$i]['ANSWERID']=$row['ANSWERID'];
	$result['ANSWERS'][$i]['ANSWER']=$row['ANSWER'];
	$result['ANSWERS'][$i]['USERID']=$row['USERID'];
	$result['ANSWERS'][$i]['USERNAME']=$row['USERNAME'];
	$result['ANSWERS'][$i]['ANSWERTIME']=$row['ANSWERTIME'];
	$result['ANSWERS'][$i]['BOOKID']=$row['BOOKID'];
	$result['ANSWERS'][$i]['EDITTIME']=$row['EDITTIME'];


	$sql2 = $conn->prepare("SELECT QC.QUESTIONID,QC.COMMENTID,QC.COMMENT,QC.COMMENTTIME,QC.USERID,QC.USERNAME FROM QUESTIONS_COMMENTS AS QC WHERE QC.QUESTIONID = :QUESTIONID");
	$sql2->bindParam('QUESTIONID',$row['QUESTIONID'],PDO::PARAM_INT);
	$sql2->execute();

	$j=0;
	while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
		$result['QUESTION']['COMMENTS']=array();
		$result['QUESTION']['COMMENTS'][$j]=array();
		$result['QUESTION']['COMMENTS'][$j]['QUESTIONID']=$row2['QUESTIONID'];
		$result['QUESTION']['COMMENTS'][$j]['COMMENTID']=$row2['COMMENTID'];
		$result['QUESTION']['COMMENTS'][$j]['COMMENT']=$row2['COMMENT'];
		$result['QUESTION']['COMMENTS'][$j]['COMMENTTIME']=$row2['COMMENTTIME'];
		$result['QUESTION']['COMMENTS'][$j]['USERID']=$row2['USERID'];
		$result['QUESTION']['COMMENTS'][$j]['USERNAME']=$row2['USERNAME'];
		$j++;
	}

	$sql3 = $conn->prepare("SELECT AC.ANSWERID,AC.COMMENTID,AC.COMMENT,AC.COMMENTTIME,AC.USERID,AC.USERNAME FROM ANSWERS_COMMENTS AS AC WHERE AC.ANSWERID = :ANSWERID");
	$sql3->bindParam('ANSWERID',$row['ANSWERID'],PDO::PARAM_INT);
	$sql3->execute();

	$j=0;
	while($row3 = $sql3->fetch(PDO::FETCH_ASSOC)){
		$result['ANSWERS'][$i]['COMMENTS']=array();
		$result['ANSWERS'][$i]['COMMENTS'][$j]=array();
		$result['ANSWERS'][$i]['COMMENTS'][$j]['ANSWERID']=$row3['ANSWERID'];
		$result['ANSWERS'][$i]['COMMENTS'][$j]['COMMENTID']=$row3['COMMENTID'];
		$result['ANSWERS'][$i]['COMMENTS'][$j]['COMMENT']=$row3['COMMENT'];
		$result['ANSWERS'][$i]['COMMENTS'][$j]['COMMENTTIME']=$row3['COMMENTTIME'];
		$result['ANSWERS'][$i]['COMMENTS'][$j]['USERID']=$row3['USERID'];
		$result['ANSWERS'][$i]['COMMENTS'][$j]['USERNAME']=$row3['USERNAME'];
		$j++;
	}
	$i++;
}
    
$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8020' );
echo json_encode ( $result );


});
?>