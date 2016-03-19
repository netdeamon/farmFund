<?php
$app->post ( "/getUserQuestions", function () use($app) {


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
$sql = $conn->prepare("SELECT QUESTIONID,QUESTIONFROMPAGENO,QUESTIONTOPAGENO,QUESTIONREF,QUESTIONTITLE,QUESTION,USERID,USERNAME,BOOKID,QUESTIONTIME FROM QUESTIONS WHERE USERID = :USERID ORDER BY QUESTIONTIME DESC LIMIT :PAGENO , :RESULTS_PER_PAGE");
$sql->bindParam('USERID', $userid, PDO::PARAM_INT);
$sql->bindParam('PAGENO', $pageno, PDO::PARAM_INT);
$sql->bindParam('RESULTS_PER_PAGE', $results_per_page, PDO::PARAM_INT);
$sql->execute();

$result = array();
$i=0;
while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {
	$result[$i] = array();
	$result[$i]['QUESTIONID']=$row['QUESTIONID'];
	$result[$i]['QUESTIONFROMPAGENO']=$row['QUESTIONFROMPAGENO'];
	$result[$i]['QUESTIONTOPAGENO']=$row['QUESTIONTOPAGENO'];
	$result[$i]['QUESTIONREF']=$row['QUESTIONREF'];
	$result[$i]['QUESTIONTITLE']=$row['QUESTIONTITLE'];
	$result[$i]['QUESTION']=$row['QUESTION'];
	$result[$i]['USERID']=$row['USERID'];
	$result[$i]['USERNAME']=$row['USERNAME'];
	$result[$i]['BOOKID']=$row['BOOKID'];
	$result[$i]['QUESTIONTIME']=$row['QUESTIONTIME'];
	$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
	$title=str_replace($chars, "-",$row['QUESTIONTITLE']);	
	$title=str_replace(" ", "-",$title);
	$title=str_replace("+", "-", $title);
	$result[$i]['QUESTIONLINK']=urlencode(base64_encode($row['QUESTIONID'])).'/'.$title;
	$i++;
}
    
$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8020' );
echo json_encode ( $result );


});
?>