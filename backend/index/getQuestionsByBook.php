<?php //$app->get("/getAllQuestions(/)(:bookname(/))(:pageno(/)(:results_per_page(/)))",function($bookname,$pageno,$results_per_page) use ($app){
$app->get("/getQuestionsByBook",function() use ($app){
	
	$bookname=$app->request()->get('bookname');
	$pageno=$app->request()->get('pageno');
	$results_per_page=$app->request()->get('results_per_page');
	
	
	if(!$pageno){
		$pageno = 0;
	}
	else{
		$pageno = intval(abs($pageno));
		$pageno = (($pageno - 1)* 10);
	}
	if(!$results_per_page){
		$results_per_page = 10;	
	}
	else{
		$results_per_page = intval(abs($results_per_page));
	}
	if($pageno<0){
		$pageno = 0;
	}
	if($results_per_page <= 0){
		$results_per_page = 1;
	}
	if(!$bookname){
	$bookname = "";
	}
	$username="root";
	$password="";
    $conn = new PDO('mysql:host=localhost;dbname=discu6zu_discuss-book', $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
     

    $s = $conn->prepare("select questionid,questiontitle,question,questiontime+INTERVAL 330 MINUTE,questionfrompageno,questiontopageno,questionref,username,bookid from questions where bookid IN (select bookid from booksdetails where bookname LIKE CONCAT('%', :bookname, '%')) and questionid IN ( SELECT  `quesid` FROM  `index` ) and questiontime+INTERVAL 330 MINUTE < NOW()+INTERVAL 330 MINUTE ORDER BY questiontime DESC LIMIT :pageno,:results_per_page");
	
	$s->bindParam('pageno', $pageno, PDO::PARAM_INT);
	$s->bindParam('results_per_page', $results_per_page, PDO::PARAM_INT);
	$s->bindParam('bookname', $bookname, PDO::PARAM_STR);
    $s->execute();
	
	$index = array();
	$i=0;
	while($rw=$s->fetch(PDO::FETCH_ASSOC)){
		$index[$i]=array();
		$index[$i]['question']=array();
		$index[$i]['question']['questionid']=$rw['questionid'];
		$index[$i]['question']['questiontitle']=$rw['questiontitle'];
		$index[$i]['question']['question']=$rw['question'];
		$index[$i]['question']['questiontime']=$rw['questiontime+INTERVAL 330 MINUTE'];
		$index[$i]['question']['questionfrompageno']=$rw['questionfrompageno'];
		$index[$i]['question']['questiontopageno']=$rw['questiontopageno'];
		$index[$i]['question']['questionref']=$rw['questionref'];
		$index[$i]['question']['username']=$rw['username'];
		$index[$i]['question']['questionref']=$rw['questionref'];
		
		$query2=$conn->prepare("select `gbookid`,`bookname`,`bookauthor`,`bookauthor2`,`bookauthor3`,`bookauthor4`,`bookpublication` from `booksdetails` where bookid=:id");
			$query2->execute(array('id'=>$rw['bookid']));
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
	$app->response()->header('Content-Type', 'application/json');
	echo json_encode($index);

});

?>