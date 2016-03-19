<?php
// $app->get("/getAllQuestions(/)(:bookname(/))(:pageno(/)(:results_per_page(/)))",function($bookname,$pageno,$results_per_page) use ($app){
$app->get ( "/getItemsByFilter", function () use($app,$conn) {

	$bookname=$app->request()->get('bookname');
	$pageno = $app->request ()->get ( 'pageno' );
	$results_per_page = $app->request ()->get ( 'results_per_page' );

	if(!$bookname){
	$bookname = "";
	}
	if (! $pageno) {
		$pageno = 0;
	} else {
		$pageno = intval ( abs ( $pageno ) );
		$pageno = (($pageno - 1) * 10);
	}
	if (! $results_per_page) {
		$results_per_page = 20;
	} else {
		$results_per_page = intval ( abs ( $results_per_page ) );
	}
	if ($pageno < 0) {
		$pageno = 0;
	}
	if ($results_per_page <= 0) {
		$results_per_page = 1;
	}

	$s = $conn->prepare("select questionid,questiontitle,question,questiontime+INTERVAL 330 MINUTE,questionfrompageno,questiontopageno,questionref,username,bookid,userid from questions where bookid IN (select bookid from booksdetails where bookname LIKE CONCAT('%', :bookname, '%')) and questionid IN ( SELECT  `quesid` FROM  `index` ) and questiontime+INTERVAL 330 MINUTE < NOW()+INTERVAL 330 MINUTE ORDER BY questiontime DESC LIMIT :pageno,:results_per_page");

$s->bindParam('pageno', $pageno, PDO::PARAM_INT);
$s->bindParam('results_per_page', $results_per_page, PDO::PARAM_INT);
$s->bindParam('bookname', $bookname, PDO::PARAM_STR);
	$s->execute();

$index = array();
$i=0;
while($row1=$s->fetch(PDO::FETCH_ASSOC)){
	$index [$i] = array();
	$index [$i] ['type'] = "Q";
	$index [$i] ['item'] = array ();
	$index [$i] ['item'] ['type'] = 0;
	$index [$i] ['item'] ['id'] = $row1 ['questionid'];
	$index [$i] ['item'] ['title'] = $row1 ['questiontitle'];
	$index [$i] ['item'] ['time'] = $row1 ['questiontime+INTERVAL 330 MINUTE'];
	$index [$i] ['item'] ['frompageno'] = $row1 ['questionfrompageno'];
	$index [$i] ['item'] ['topageno'] = $row1 ['questiontopageno'];
	$index [$i] ['item'] ['ref'] = $row1 ['questionref'];
	$index [$i] ['item'] ['userid'] = $row1 ['userid'];
	$index [$i] ['item'] ['username'] = $row1 ['username'];
	$index [$i] ['item'] ['bookid'] = $row1 ['bookid'];
	$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
	$title=str_replace($chars, "-",$row1['questiontitle']);
	$title=str_replace(" ", "-",$title);
	$title=str_replace("+", "-", $title);
	$index[$i]['item']['link']=urlencode(base64_encode($row1['questionid'])).'/'.$title;

	$query2=$conn->prepare("select `gbookid`,`bookname`,`bookauthor`,`bookauthor2`,`bookauthor3`,`bookauthor4`,`bookpublication` from `booksdetails` where bookid=:id");
		$query2->execute(array('id'=>$row1['bookid']));
	$index[$i]['book']=array();
		while($row2=$query2->fetch(PDO::FETCH_ASSOC)){
			$index[$i]['book']=array();
			$index[$i]['book']['gbookid']=$row2['gbookid'];
			$index[$i]['book']['bookname']=$row2['bookname'];
			$index[$i]['book']['bookauthor']=$row2['bookauthor'];
			$index[$i]['book']['bookauthor2']=$row2['bookauthor2'];
			$index[$i]['book']['bookauthor3']=$row2['bookauthor3'];
			$index[$i]['book']['bookauthor4']=$row2['bookauthor4'];
			$index[$i]['book']['bookpublication']=$row2['bookpublication'];
		}

	$i++;
}

$sql = $conn->prepare("SELECT `BOOKTIPID`, `PAGENOFROM`, `PAGENOTO`, `BOOKTIPREF`, `BOOKTIPTITLE`, `BOOKTIP`, `USERNAME`, `USERID`, `BOOKID`, `TIME`, `BOOKNAME`, `SUBNAME`, `SUBID`, `SEM`, `EDITTIME` FROM `BOOKTIP` where `BOOKNAME` LIKE CONCAT('%', :bookname, '%') or bookid IN (select bookid from booksdetails where bookname LIKE CONCAT('%', :bookname2, '%')) ORDER BY `TIME` DESC LIMIT :PAGENO , :RESULTS_PER_PAGE ");

$sql->bindParam('PAGENO', $pageno, PDO::PARAM_INT);
$sql->bindParam('RESULTS_PER_PAGE', $results_per_page, PDO::PARAM_INT);
$sql->bindParam('bookname', $bookname, PDO::PARAM_STR);
$sql->bindParam('bookname2', $bookname, PDO::PARAM_STR);
$sql->execute();


		while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {

			$index [$i] = array ();
			$index [$i] ['type'] = "N";
			$index [$i] ['item'] = array ();
			$index[$i]['item']['id']=$row['BOOKTIPID'];
			$index[$i]['item']['frompageno']=$row['PAGENOFROM'];
			$index[$i]['item']['ref']=$row['BOOKTIPREF'];
			$index[$i]['item']['topageno']=$row['PAGENOTO'];
			$index[$i]['item']['title']=$row['BOOKTIPTITLE'];
			$index[$i]['item']['userid']=$row['USERID'];
			$index[$i]['item']['username']=$row['USERNAME'];
			$index[$i]['item']['time']=$row['TIME'];
			$index[$i]['item']['bookid']=$row['BOOKID'];
			$index[$i]['item']['subname']=$row['SUBNAME'];
			$index[$i]['item']['subid']=$row['SUBID'];
			$index[$i]['item']['edittime']=$row['EDITTIME'];
			$index[$i]['item']['sem']=$row['SEM'];
			$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
			$title=str_replace($chars, "-",$row['BOOKTIPTITLE']);
			$title=str_replace(" ", "-",$title);
			$title=str_replace("+", "-", $title);
			$index[$i]['item']['link']=urlencode(base64_encode($row['BOOKTIPID'])).'/'.$title;


			$query2 = $conn->prepare ( "select `gbookid`,`bookname`,`bookauthor`,`bookauthor2`,`bookauthor3`,`bookauthor4`,`bookpublication` from `booksdetails` where bookid=:id" );
			$query2->execute ( array (
					'id' => $row ['BOOKID']
			) );

			while ( $row2 = $query2->fetch ( PDO::FETCH_ASSOC ) ) {
				$index [$i] ['book'] = array ();
				$index [$i] ['book'] ['gbookid'] = $row2 ['gbookid'];
				$index [$i] ['book'] ['bookname'] = $row2 ['bookname'];
				$index [$i] ['book'] ['bookauthor'] = $row2 ['bookauthor'];
				$index [$i] ['book'] ['bookauthor2'] = $row2 ['bookauthor2'];
				$index [$i] ['book'] ['bookauthor3'] = $row2 ['bookauthor3'];
				$index [$i] ['book'] ['bookauthor4'] = $row2 ['bookauthor4'];
				$index [$i] ['book'] ['bookpublication'] = $row2 ['bookpublication'];
			}

			$i++;
		}


		$app->response ()->header ( 'Content-Type', 'application/json' );
		$app->response ()->header ( 'access-control-allow-origin', '*' );

		usort($index, function($a,$b){
			$t1 = strtotime($a["item"]["time"]);
	    $t2 = strtotime($b["item"]["time"]);
		  return ($t2 - $t1);
		});
		echo json_encode ( $index );


} );

?>
