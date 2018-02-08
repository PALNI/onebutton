<?php
//set_error_handler("warning_handler", E_WARNING);
//Request data from Availability API
   use OCLC\Auth\WSKey;
   use OCLC\User;
   use Guzzle\Http\Client;
   
$config = include('config.php');

/*Create log directory and file*/
function createLogs() {
//if doesn't exist, create logs directory, set permissions
    if (!file_exists('logs')) {
    mkdir('logs', 0755, true);
	}
    //create log file 
    $today = date("m-d-Y");
    $logfile = "logs/". $today ."-log.txt";
    touch($logfile);
    return $logfile;
}


/*lookUp function queries WorldCat Availability API for holdings and availability   
and returns institutional holdings as well as holdings available across PALShare*/
function lookUp() {
    $config = include('config.php');
    $oclcNum = "";
    $bookTitle = "null";
    $authorLast = "";
    $authorFirst = "";
    $isbn = "";
    $pubDate = "";
    
    //define openURL parameters from generic URL from WorldCat Local / Discovery
    if (isset($_GET['rfe_dat'])) {
       $oclcNum = $_GET['rfe_dat'];
    }
    if (isset($_GET['rft_btitle'])) {
      $bookTitle = urlencode($_GET['rft_btitle']);
    }
    if (isset($_GET['rft_aulast'])) {
      $authorLast = urlencode($_GET['rft_aulast']);
    }
    if (isset($_GET['rft_aufirst'])) {
      $authorFirst = urlencode($_GET['rft_aufirst']);
    }
    if (isset($_GET['rft_aucorp'])) {
      $authorLast = urlencode($_GET['rft_aucorp']);
    }
    if (isset($_GET['rft_isbn'])) {
      $isbn = urlencode($_GET['rft_isbn']);
    }
    if (isset($_GET['rft_date'])) {
      $pubDate = urlencode($_GET['rft_date']);
    }
    $today = date("m-d-Y");
    //pull all openURL parameters
    $page = $_SERVER['REQUEST_URI'];
    $query = parse_url($page, PHP_URL_QUERY);
    parse_str($query);
    parse_str($query, $arr);
    
    
   //Require dependencies for using OCLC APIs
   require_once('../vendor/autoload.php');
   //configuration file, API keys, etc.
   require('config.php');
   //include file of locations that don't lend for PALShare
   require('nonlending.php');
   
   //build Availability API request authorization
   $wskey = new WSKey($config['key'], $config['secret']);
   $url = 'https://worldcat.org/circ/availability/sru/service?x-registryId=' . $config['institutionId'] . '&x-return-group-availability=true' . '&query=no:ocm' . $oclcNum;
   $user = new User($config['institutionId'], $config['principleId'], $config['principleIdNs']);
   $options = array('user'=> $user);

   $authorizationHeader = $wskey->getHMACSignature('GET', $url, $options);
   
   $client = new Guzzle\Http\Client();
   $client->get('config/curl/' . CURLOPT_SSLVERSION, 3);
   $headers = array();
   $headers['Authorization'] = $authorizationHeader;
   $request = $client->createRequest('GET', $url, $headers);

   //send request, get back XML
   try {
   		$response = $request->send();
        $xml = $response->getBody(TRUE);
        $xmlObj = simplexml_load_string($xml);
        //Get the title of the book for use on results display screen
        	$titlea = "";
        	$titleb = "";
        	$titlec = "";
        	$bookTitle = "";
        	$titlea = $xmlObj->xpath('//datafield[@tag="245"]/subfield[@code="a"]');
        	$titleb = $xmlObj->xpath('//datafield[@tag="245"]/subfield[@code="b"]');
        	$titlec = $xmlObj->xpath('//datafield[@tag="245"]/subfield[@code="c"]');
        		if (!empty($titlea)) {
          			$bookTitle = $titlea[0];
        		}
        		if (!empty($titleb)) {
          			$bookTitle = $bookTitle . $titleb[0];
        		}
        		if (!empty($titlec)) {
          			$bookTitle = $bookTitle . $titlec[0];
        		}
        	$title = $bookTitle;
        
        //Get holdings and write to line array
		if(isset($xmlObj->records->record->recordData->opacRecord->holdings->holding))  {
		    $institutions = array();
		    $localloc = array();
		    $availability = array();
		    $callnums = array();
		    $shelfs = array();
		    $line = array();
		    foreach ($xmlObj->records->record->recordData->opacRecord->holdings->holding as $holding) {
		      $institution = (string) $holding->nucCode;
		      $localloc = $holding->localLocation;
		      $shelf = (string) $holding->shelvingLocation;
		      $callnum = (string) $holding->callNumber;
		      $instavailcount = array();
		      $datapoints = array();
		      foreach ($holding->circulations->circulation as $circulation) {
		          $institutions[] = $institution;
		          $localloc[] = $localloc;
		          $shelfs[] = $shelf;
		          $callnums[] = $callnum;
		      	  $avail = (string) $circulation->availableNow->attributes();
			      $availability[] = $avail;
		          $barcode = $circulation->itemId;
		          $line[] = array("inst"=>$institution,"loc"=>$shelf,"callnum"=>$callnum, "avail"=>$avail, "barcode"=>$barcode, "localloc"=>$localloc);
			      }       
            }
        
        //if holdings are found, check each holding line array for availability
        if (is_array($line)) {
          $innerLine = array();
          foreach ($line as $innerLine) {
    		//  Check type
    		if (is_array($innerLine)){
       		 	//  Scan through inner loop
        		foreach ($innerLine as $value) {
        		  $instLine = $innerLine["inst"];
        		  $locLine = $innerLine["loc"];
        		  $callLine = $innerLine["callnum"];
        		  $availLine = $innerLine["avail"];
        		  $locallocLine = $innerLine["localloc"];
        		  //Use MD5 hash because some branch codes have special characters, see nonlending.php
        		  $locallocLine = md5($locallocLine);
        		}
        			//check the branch code (locallocLine) and loc (locLine) against the nonlending locs in nonlending.php
        			$found=false;
        		
        			foreach ($palninonlending as $ke=>$va) {
        		  		if($ke == $locallocLine) {
        		    		foreach($va as $k=>$v) {
        		      				if($v == $locLine) {
        		        				$found=true;
        		      				}
        		      		}		
        		        }
        		     }
        		//if the branch code/loc match values in nonlending.php, skip; otherwise, check availability
        		if ($found == true) {
        		  continue;
        		} else {
        		  //if there are items available, check to make sure items are actually available; if so, create array of available items
        		  	if (($instLine == $config['institutionCode']) && ($availLine !== '0')) { 
        		      		$instItems[] = "<p><strong>Call Number:</strong>  " . $callLine . "  <br /><strong>Shelving Location:</strong>  " . $locLine . '</p>';
        		    	}  elseif (($instLine !== $config['institutionCode']) && ($availLine !== '0')) {
        		        //create nested array with shelvingLocation and availability of Resource-Sharing held items to be passed to resShare function
        		         	$resItems[] = array('callnumber'=>$callLine, 'shelvinglocation'=>$locLine, 'availability'=>$availLine, 'instl'=>$instLine);
        		         } 
          		} 	   
           }
         }
        }
     }
    //end try
    }
    //catch errors (e.g., API is down) and forward request to ILL form
    catch (\Exception $e) {
      //Write to log file
		        $current = "API Failure ILL: " . $openILL ."\n";
          		$current .= file_get_contents($logfile);
          		file_put_contents($logfile, $current);
	  Header( 'Location: '. $openILL  ) ;
    }
return array('instItems' => $instItems, 'institutions' => $resItems, 'oclcNum' => $oclcNum, 'query' => $query, 'bookTitle' => $bookTitle);
}


/*showPage function displays institutional holdings and/or stackMap, if stackMap is available*/
#function showPage($lookUpResult) {
function showPage($instItems,$bookTitle,$instoclc) {
                    $config = include('config.php');
                    //TODO - fix the resshareURL; the $lookUpResult parameter is not just the OCLC number
                    $resshareurl = 'https://' . $config['institutionURL'] .'.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=638&queryString=' . $bookTitle . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $instoclc . '/circ/hold/PLACE_HOLD';
                    echo '<html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Get It!</title><link rel="stylesheet" href="../bootstrap/css/sticky-footer-navbar.css"><link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css"><script>
					function getReferrer() {
    				var x = document.referrer;
    				document.getElementById("refer").setAttribute("href",x);
					}
					</script><script src="https://use.fontawesome.com/6955d8ad4f.js"></script></head><body onload="getReferrer()"><!-- Fixed navbar -->
    				<nav class="navbar navbar-default navbar-fixed-top">
      					<div class="container">
     					 	<div class="navbar-header">
          						<a class="navbar-brand" href="#">Get It!</a>
        					</div>
        					<div id="navbar" class="collapse navbar-collapse">
          						<ul class="nav navbar-nav">
           							<li class="active"><a id="refer"><i class="fa fa-arrow-circle-left"></i>  Back to Search Results</a></li>
       							</ul>
        					</div>
        					<!--/.nav-collapse -->
      					</div>
    				</nav>
    				<div class="container">
    				<p class="lead">This item is available in your library!</p>';
                                if ($config[onshelfHold] == 'true') {
                                  echo '<p class="lead"><a href="'. $resshareurl . '">Place a hold</a> and pickup the item at the circulation desk OR</p>';
                                }
                                echo '<p class="lead">Find it on the shelf at the call number and shelving location shown below:</p>';
                                echo '<p><strong>Title:</strong>  ' . $bookTitle . '</p>';
    				foreach ($instItems as $displayItem) {
    				  echo $displayItem;
    				}

    				echo '<!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
    				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    				<!-- Include all compiled plugins (below), or include individual files as needed -->
    				<script src="../bootstrap/js/bootstrap.min.js"></script></body></html>';
}




/*function create resource sharing place hold URL*/
function resShare($ressearch,$resoclc) {
      
      //include config and nonlending settings
      	$config = include('config.php');
      	require('nonlending.php');
	  	$resshareurl = 'https://' . $config['institutionURL'] .'.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=638&queryString=' . $ressearch . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $resoclc. '/circ/hold/PLACE_HOLD';
	  
	  //Write to log file
	  	$createLogsResult = createLogs();
	  	
	  	$current = file_get_contents($createLogsResult);
	  	$current .= "PALShare Hold: " . $resshareurl ."\n";
	  	file_put_contents($createLogsResult,$current);
	  
	  
	  //Direct to Resource Sharing URL
	  	Header( 'Location: https://' . $config['institutionURL'] .'.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=638&queryString=' .$ressearch. '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $resoclc . '/circ/hold/PLACE_HOLD' );
}





/*function directs request to ILL Form
Pass OCLC Number or query from original request*/
function requestILL($illquery,$illoclc) {
	//include config file
		$config = include('config.php');
        //if using OCLC ILL, only need oclcNumber
   		if ($config['oclcILL'] == 'true') {
      		$openILL = $config['illForm'] . $illoclc;
      		
      		//Write to log file
	  			$createLogsResult = createLogs();
	  	
	  			$current = file_get_contents($createLogsResult);
	  			$current .= "ILLRequest " . $openILL ."\n";
	  			file_put_contents($createLogsResult,$current);
      		
      		
      		Header( 'Location: '. $openILL  ) ;
      	//if using generic OpenURL illForm, need full openURL query
    	} else {
      		$openILL = $config['illForm'] . $illquery;
      		Header( 'Location: '. $openILL  ) ;
    }
}


/*Process Request and Call Functions*/

//Call OCLC API lookup function to get associative array results
$lookUpResult = lookUp();

//if held and available in instition, show availability and stack map
//ToDo - add logging here, so we can assess how many times, if any, this screen shows
if (is_array($lookUpResult['instItems'])) {
  $instItems = $lookUpResult['instItems'];
  $bookTitle = $lookUpResult['bookTitle'];
  $instoclc = $lookUpResult['oclcNum'];
  showPage($instItems,$bookTitle,$instoclc);
  
  
//if PALShare holdings, direct to PALShare hold
//institutions is resItems of lookUpResult function (available resource sharing items)
} elseif (is_array($lookUpResult['institutions'])) {
  $ressearch = $lookUpResult['bookTitle'];
  $resoclc = $lookUpResult['oclcNum'];
  //comment out for debugging to only show resource sharing array output
  resShare($ressearch,$resoclc);
  //Uncomment for debugging
  //print_r($lookUpResult['institutions']);
  //print_r($lookUpResult['bookTitle']);
  
//No institution or PALShare holdings, send user to ILL request
} else {
  $illquery = $lookUpResult['query'];
  $illoclc = $lookUpResult['oclcNum'];
  //comment out for debugging
  requestILL($illquery,$illoclc);
  //Uncomment for debugging
  //print_r($lookUpResult);
}
    
    //Handle Warnings
        function warning_handler($errno, $errstr) { 
	    //Header( 'Location: '. $openILL  ) ;
    }
   
?>
