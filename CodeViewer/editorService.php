<?php 

	//---------------------------------------------------------------------------------------------------------------
	// editorService - Saves and Reads content for Code Editor
	//---------------------------------------------------------------------------------------------------------------

	// Check if example exists and then do operation

	date_default_timezone_set("Europe/Stockholm");

	// Include basic application services!
	include_once("../../coursesyspw.php");	
	include_once ("../Shared/sessions.php");
	include_once ("../Shared/basic.php");
	include_once ("../Shared/courses.php");
	
	// Connect to database and start session
	dbConnect();
	session_start();
	
	$exampleid=getOP('exampleid');
	$boxid=getOP('boxid');
	$opt=getOP('opt');
	$cid=getOP('courseid');
	$cvers=getOP('cvers');
	$templateno=getOP('templateno');
		
	$debug="NONE!";	

	if(isset($_SESSION['uid'])){
			$userid=$_SESSION['uid'];
	}else{
			$userid="1";		
	} 
	if(checklogin() && (hasAccess($userid, $cid, 'w'))){
			$writeaccess="w";
	}else{
			$writeaccess="s";	
	}
	
	$appuser=(array_key_exists('uid', $_SESSION) ? $_SESSION['uid'] : 0);

	// Make sure there is an exaple
	$cnt=0;
	$query = "SELECT exampleid,examplename,cid,cversion,public FROM codeexample WHERE exampleid='$exampleid';";	
	$result=mysql_query($query);
	if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
	while ($row = mysql_fetch_assoc($result)){
			$cnt++;
			$exampleid=$row['exampleid'];
			$examplename=$row['examplename'];
			$courseID=$row['cid'];
			$cversion=$row['cversion'];
			$public=$row['public'];
	}
	
	if($cnt>0){

				//------------------------------------------------------------------------------------------------
				// Perform Update Action
				//------------------------------------------------------------------------------------------------

				if(checklogin() && (hasAccess($_SESSION['uid'], $cid, 'w') || isSuperUser($_SESSION['uid']))) {
						$writeaccess="w";
						if(strcmp('SETTEMPL',$opt)===0){
									// Add word to wordlist
									$query = "UPDATE codeexample SET templateid='$templateno' WHERE exampleid='$exampleid' and $cid='$cid' and cversion='$cvers';";		
									$result=mysql_query($query);
									if (!$result) err("SQL Query Error: ".mysql_error(),"Error updating Wordlist!");						

									// We have two boxes. Create two boxes to start with
									if($templateno==1||$templateno==2) $boxcnt=2;
									if($templateno==3||$templateno==4) $boxcnt=3;
									if($templateno==5) $boxcnt=4;									
									
									// Create appropriate number of boxes
									for($i=1;$i<$boxcnt+1;$i++){
											$ruery = "INSERT INTO box(boxid,exampleid,boxtitle,boxcontent,settings,filename) VALUES ('$i','$exampleid','Title','Code','[viktig=1]','js1.js');";		
											$result=mysql_query($ruery);
											if (!$result) err("SQL Query Error: ".mysql_error(),"Error updating Wordlist!");						
									}
						}
			}
	
			//------------------------------------------------------------------------------------------------
			// Retrieve Information			
			//------------------------------------------------------------------------------------------------	
				
			// Read exampleid, examplename and runlink etc from codeexample and template
			$examplename="";
			$templateid="";
			$stylesheet="";
			$numbox="";
			$exampleno=0;
			$playlink="";
			$public="";
			$entryname="";
			$query = "SELECT exampleid,examplename,sectionname,runlink,public,template.templateid as templateid,stylesheet,numbox FROM codeexample LEFT OUTER JOIN template ON template.templateid=codeexample.templateid WHERE exampleid=$exampleid and cid='$courseID'";		
			$result=mysql_query($query);
			if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
			while ($row = mysql_fetch_assoc($result)){
					$examplename=$row['examplename'];
					$exampleno=$row['exampleid'];
					$public=$row['public'];
					$playlink=$row['runlink'];
					$sectionname=$row['sectionname'];
					$templateid=$row['templateid'];
					$stylesheet=$row['stylesheet'];
					$numbox=$row['numbox'];					
			}
									
			// Read ids and names from before/after list
			$beforeafter = array();
			$query = "select exampleid,sectionname,examplename,beforeid,afterid from codeexample where cid='".$cid."' and cversion='".$cvers."' order by sectionname,examplename;";
			$result=mysql_query($query);
			if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
			while ($row = mysql_fetch_assoc($result)){
		  		$beforeafter[$row['exampleid']]=array($row['exampleid'],$row['sectionname'],$row['examplename'],$row['beforeid'],$row['afterid']);
			}  
									
			// iteration to find after examples - We start with $exampleid and at most 5 are collected
			$cnt=0;
			$forward_examples = array();	
			$currid=$exampleid;
			do{
					if(isset($beforeafter[$currid])){
							$currid=$beforeafter[$currid][4];
					}else{
							$currid=null;
					}					
					if($currid!=null){
							array_push($forward_examples,$beforeafter[$currid]);
							$cnt++;
					}
			}while($currid!=null&&$cnt<5);

			// iteration to find before examples - We start with $exampleid and at most 5 are collected 
			$backward_examples = array();	
			$currid=$exampleid;
			$cnt=0;
			do{
					if(isset($beforeafter[$currid])){
							$currid=$beforeafter[$currid][3];
					}else{
							$currid=null;
					}					
					if($currid!=null){
							array_push($backward_examples,$beforeafter[$currid]);
							$cnt++;
					}
			}while($currid!=null&&$cnt<5);
				  
		  // Read important lines
			$imp=array();
			$query = "SELECT boxid,istart,iend FROM improw WHERE exampleid=$exampleid ORDER BY istart;";
			$result=mysql_query($query);
			if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
			while ($row = mysql_fetch_assoc($result)){
		  		array_push($imp,array($row['boxid'],$row['istart'],$row['iend']));
			}  
		
			// Get all words for each wordlist
			$words = array();
			$query = "SELECT wordlistid,word,label FROM word ORDER BY wordlistid";
			$result=mysql_query($query);
			if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
			while ($row = mysql_fetch_assoc($result)){
		  		array_push($words,array($row['wordlistid'],$row['word'],$row['label']));					
			}
			
			// Get all wordlists
			$wordlists=array();
			$query = "SELECT wordlistid, wordlistname FROM wordlist ORDER BY wordlistid;";
			$result=mysql_query($query);
			if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
			while ($row = mysql_fetch_assoc($result)){
		  		array_push($wordlists,array($row['wordlistid'],$row['wordlistname']));					
			} 
			
		  // Read important wordlist
			$impwordlist=array();
			$query = "SELECT word,label FROM impwordlist WHERE exampleid=$exampleid ORDER BY word;";
			$result=mysql_query($query);
			if (!$result) err("SQL Query Error: ".mysql_error(),"Field Querying Error!" . __LINE__);	
			while ($row = mysql_fetch_assoc($result)){
		  		array_push($impwordlist,$row['word']);					
			}  
			
			// Read Directory - Codeexamples
			$directory=array();
			$dir = opendir('./codeupload');
		  while (($file = readdir($dir)) !== false) {
		  	if(endsWith($file,".js")){
		    		array_push($directory,$file);		
		    }
		  }  

      // Read Directory - Images
      $images=array();
      $img_dir = opendir('./imgupload');
      while (($img_file = readdir($img_dir)) !== false) {
          if(endsWith($img_file,".png")){
              array_push($images,$img_file);
          }
      }

			// Collect information for each box
			$box=array();   // get the primary keys for all types kind of boxes.
			$query = "SELECT boxid,boxcontent,boxtitle,filename,wordlistid,segment FROM box WHERE exampleid=$exampleid ORDER BY boxid;";
			$result=mysql_query($query);

			while ($row = mysql_fetch_assoc($result)){
					$boxcontent=strtoupper($row['boxcontent']);
					$filename=$row['filename'];
					$content="";					
					if(strcmp("DOCUMENT",$boxcontent)===0){
							$content=$row['segment'];
					}else{
							$filename="./codeupload/".$filename;
							$handle = @fopen($filename, "r");
							if ($handle) {
							    while (($buffer = fgets($handle, 1024)) !== false) {
											$content=$content.$buffer;
									}
							    if (!feof($handle)) {
						       		$content.="Error: Unexpected end of file ".$filename."\n";			    
							    }
							    fclose($handle);
							}
					}
					array_push($box,array($row['boxid'],$boxcontent,$content,$row['wordlistid'],$row['boxtitle']));
			}						
				
			$array = array(
					'before' => $backward_examples,
					'after' => $forward_examples,
					'templateid' => $templateid,
					'stylesheet' => $stylesheet,
					'numbox' => $numbox,
					'box' => $box,
					'improws' => $imp,
					'impwords' => $impwordlist,
					'directory' => $directory,
					'examplename'=> $examplename,
					'sectionname'=> $sectionname,
					'playlink' => $playlink,
					'exampleno' => $exampleno,
					'words' => $words,
					'wordlists' => $wordlists, 
					'images' => $images,
					'writeaccess' => $writeaccess,
					'debug' => $debug,
					'beforeafter' => $beforeafter, 
					'public' => $public
			);
			
			echo json_encode($array);


	}else{
			$array = array(
					'debug' => "ID does not exist" 
			);
			
			echo json_encode($array);

	}
	
?>
