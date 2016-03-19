<?php
$app->post ( "/getUserAnswers", function () use($app) {

/*RETURNS ANSWER
THE QUESTION
THE BOOK DETAILS
ANSWER VOTES
*/
/*get the userid*/
$userid=$app->request()->post('userid');
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
$sql = $conn->prepare("SELECT Q.QUESTIONID,Q.QUESTIONTITLE,A.ANSWERID,A.ANSWER,A.USERID,A.USERNAME,A.ANSWERTIME,A.BOOKID,A.EDITTIME FROM QUESTIONS_ANSWERS AS QA INNER JOIN ANSWERS AS A INNER JOIN QUESTIONS AS Q ON QA.ANSWERID = A.ANSWERID AND Q.QUESTIONID = QA.QUESTIONID AND A.USERID = :USERID LIMIT :PAGENO , :RESULTS_PER_PAGE");
$sql->bindParam('USERID', $userid, PDO::PARAM_INT);
$sql->bindParam('PAGENO', $pageno, PDO::PARAM_INT);
$sql->bindParam('RESULTS_PER_PAGE', $results_per_page, PDO::PARAM_INT);
$sql->execute();

$result = array();
$i=0;
while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {
	$result[$i] = array();
	$result[$i]['ANSWERID']=$row['ANSWERID'];
	$result[$i]['ANSWER']=$row['ANSWER'];
	$result[$i]['USERID']=$row['USERID'];
	$result[$i]['USERNAME']=$row['USERNAME'];
	$result[$i]['ANSWERTIME']=$row['ANSWERTIME'];
	$result[$i]['BOOKID']=$row['BOOKID'];
	$result[$i]['EDITTIME']=$row['EDITTIME'];
	$result[$i]['QUESTIONID']=$row['QUESTIONID'];
	$result[$i]['QUESTIONTITLE']=$row['QUESTIONTITLE'];
	$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
	$title=str_replace($chars, "-",$row['QUESTIONTITLE']);	
	$title=str_replace(" ", "-",$title);
	$title=str_replace("+", "-", $title);
	$result[$i]['NOTESLINK']=urlencode(base64_encode($row['QUESTIONID'])).'/'.$title;

	$i++;
}
    
$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8020' );
echo json_encode ( $result );


});
?>