<?php
// $app->get("/getAllQuestions(/)(:bookname(/))(:pageno(/)(:results_per_page(/)))",function($bookname,$pageno,$results_per_page) use ($app){
$app->get ( "/getItems", function () use($app,$conn) {

	$pageno = $app->request ()->get ( 'pageno' );
	$results_per_page = $app->request ()->get ( 'results_per_page' );

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


	$s = $conn->prepare ( "SELECT `id`,`quesid`,`ques_subid`,`time` from `index` where time+INTERVAL 330 MINUTE < NOW()+INTERVAL 330 MINUTE ORDER BY time DESC LIMIT :pageno,:results_per_page" );
	$index = array ();

	$i = 0;
	$s->bindParam ( 'pageno', $pageno, PDO::PARAM_INT );
	$s->bindParam ( 'results_per_page', $results_per_page, PDO::PARAM_INT );
	$s->execute ();

	while ( $rw = $s->fetch ( PDO::FETCH_ASSOC ) ) {
		$index [$i] = array ();
		$index [$i] ['type'] = "Q";
		if ($rw ['quesid'] != NULL) {
			$query1 = $conn->prepare ( "select `questionid`,`questiontitle`,questiontime+INTERVAL 330 MINUTE,`questionfrompageno`,`questiontopageno`,`questionref`,`userid`,`username`,`bookid` from `questions` where questionid=:id and questiontime+INTERVAL 330 MINUTE < NOW()+INTERVAL 330 MINUTE" );
			$query1->execute ( array (
					'id' => $rw ['quesid']
			) );

			while ( $row1 = $query1->fetch ( PDO::FETCH_ASSOC ) ) {
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

				$query2 = $conn->prepare ( "select `gbookid`,`bookname`,`bookauthor`,`bookauthor2`,`bookauthor3`,`bookauthor4`,`bookpublication` from `booksdetails` where bookid=:id" );
				$query2->execute ( array (
						'id' => $row1 ['bookid']
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

				$query4 = $conn->prepare ( "select count(answerid) from questions_answers where questionid=:id and time+INTERVAL 330 MINUTE < NOW()+INTERVAL 330 MINUTE " );
				$query4->execute ( array (
						'id' => $rw ['quesid']
				) );
				while ( $row4 = $query4->fetch ( PDO::FETCH_ASSOC ) ) {
					$index [$i] ['item'] ['count_answers'] = $row4 ['count(answerid)'];
				}
			}
		}
		if ($rw ['ques_subid'] != NULL) {
			$query5 = $conn->prepare ( "select `questiontitle`,`sem`,`subid`,`subname`,`userid`,`username`,`questionid`,questiontime+INTERVAL 330 MINUTE from `questions_subjects` where questionid=:id and questiontime+INTERVAL 330 MINUTE < NOW()+INTERVAL 330 MINUTE " );
			$query5->execute ( array (
					'id' => $rw ['ques_subid']
			) );
			while ( $row5 = $query5->fetch ( PDO::FETCH_ASSOC ) ) {
				$index [$i] ['item'] = array ();
				$index [$i] ['item'] ['type'] = 1;
				$index [$i] ['item'] ['title'] = $row ['questiontitle'];
				$index [$i] ['item'] ['id'] = $row ['questionid'];
				$index [$i] ['item'] ['time'] = $row ['questiontime+INTERVAL 330 MINUTE'];
				$index [$i] ['item'] ['username'] = $row ['username'];
				$index [$i] ['item'] ['sem'] = $row ['sem'];
				$index [$i] ['item'] ['subid'] = $row ['subid'];
				$index [$i] ['item'] ['subname'] = $row ['subname'];
			}

			$sql_answer = $conn->prepare ( "select count(replyid) from `reply` where questionid=:id and time+INTERVAL 330 MINUTE <NOW()+INTERVAL 330 MINUTE" );
			$sql_answer->execute ( array (
					'id' => $rw ['ques_subid']
			) );

			while ( $row_count = $sql_answer->fetch ( PDO::FETCH_ASSOC ) ) {
				$index [$i] ['item'] ['count_answers'] = $row_count ['count(replyid)'];
			}
		}

		$i ++;
	}

$sql = $conn->prepare("SELECT `booktipid`, `pagenofrom`, `pagenoto`, `booktipref`, `booktiptitle`, `booktip`, `username`, `userid`, `bookid`, `time`, `bookname`, `subname`, `subid`, `sem`, `edittime` from `booktip` order by `time` desc limit :pageno , :results_per_page ");

$sql->bindParam('pageno', intval($pageno,10), PDO::PARAM_INT);
$sql->bindParam('results_per_page', intval($results_per_page,10), PDO::PARAM_INT);
$sql->execute();


		while ( $row = $sql->fetch ( PDO::FETCH_ASSOC ) ) {
					$index [$i] = array ();
			$index [$i] ['type'] = "N";
				$index [$i] ['item'] = array ();
			$index[$i]['item']['id']=$row['booktipid'];
			$index[$i]['item']['frompageno']=$row['pagenofrom'];
			$index[$i]['item']['ref']=$row['booktipref'];
			$index[$i]['item']['topageno']=$row['pagenoto'];
			$index[$i]['item']['title']=$row['booktiptitle'];
			$index[$i]['item']['userid']=$row['userid'];
			$index[$i]['item']['username']=$row['username'];
			$index[$i]['item']['time']=$row['time'];
			$index[$i]['item']['bookid']=$row['bookid'];
			$index[$i]['item']['subname']=$row['subname'];
			$index[$i]['item']['subid']=$row['subid'];
			$index[$i]['item']['edittime']=$row['edittime'];
			$index[$i]['item']['sem']=$row['sem'];
			$chars=array('~','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','`','=','[',']','\\',';','.','/');
			$title=str_replace($chars, "-",$row['booktiptitle']);
			$title=str_replace(" ", "-",$title);
			$title=str_replace("+", "-", $title);
			$index[$i]['item']['link']=urlencode(base64_encode($row['booktipid'])).'/'.$title;


			$query2 = $conn->prepare ( "select `gbookid`,`bookname`,`bookauthor`,`bookauthor2`,`bookauthor3`,`bookauthor4`,`bookpublication` from `booksdetails` where bookid=:id" );
			$query2->execute ( array (
					'id' => $row ['bookid']
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

date_default_timezone_set("UTC");
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
