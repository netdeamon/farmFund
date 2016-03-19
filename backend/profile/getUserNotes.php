<?php
$app->post ( "/getUserNotes", function () use($app) {


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
$sql = $conn->prepare("SELECT `BOOKTIPID`, `PAGENOFROM`, `PAGENOTO`, `BOOKTIPREF`, `BOOKTIPTITLE`, `BOOKTIP`, `USERNAME`, `USERID`, `BOOKID`, `TIME`, `BOOKNAME`,
 `SUBNAME`, `SUBID`, `SEM`, `EDITTIME` FROM `BOOKTIP`WHERE USERID = :USERID ORDER BY `TIME` DESC LIMIT :PAGENO , :RESULTS_PER_PAGE");
$sql->bindParam('USERID', $userid, PDO::PARAM_INT);
$sql->bindParam('PAGENO', $pageno, PDO::PARAM_INT);
$sql->bindParam('RESULTS_PER_PAGE', $results_per_page, PDO::PARAM_INT);
$sql->execute();

$result = array();
$i=0;
while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {
	$result[$i] = array();
	$result[$i]['BOOKTIPID']=$row['BOOKTIPID'];
	$result[$i]['PAGENOFROM']=$row['PAGENOFROM'];
	$result[$i]['PAGENOTO']=$row['PAGENOTO'];
	$result[$i]['BOOKTIPREF']=$row['BOOKTIPREF'];
	$result[$i]['BOOKTIPTITLE']=$row['BOOKTIPTITLE'];
	$result[$i]['BOOKTIP']=$row['BOOKTIP'];
	$result[$i]['USERNAME']=$row['USERNAME'];
	$result[$i]['USERID']=$row['USERID'];
	$result[$i]['TIME']=$row['TIME'];
	$result[$i]['BOOKID']=$row['BOOKID'];
	$result[$i]['SUBNAME']=$row['SUBNAME'];
	$result[$i]['SUBID']=$row['SUBID'];
	$result[$i]['SEM']=$row['SEM'];
	$result[$i]['EDITTIME']=$row['EDITTIME'];
	$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
	$title=str_replace($chars, "-",$row['BOOKTIPTITLE']);	
	$title=str_replace(" ", "-",$title);
	$title=str_replace("+", "-", $title);
	$result[$i]['NOTESLINK']=urlencode(base64_encode($row['BOOKTIPID'])).'/'.$title;
	$i++;
}
    
$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8020' );
echo json_encode ( $result );


});
?>