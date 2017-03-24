<?php
    set_error_handler("warning_handler", E_WARNING);
    $oclcNum = "";
    $bookTitle = "";
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
    
    //if doesn't exist, create logs directory, set permissions
     if (!file_exists('logs')) {
    mkdir('logs', 0755, true);
	}
    //create log file 
    $logfile = "logs/". $today ."-log.txt";  
    
   //Require dependencies for using OCLC APIs
   require_once('../vendor/autoload.php');
   require('config.php');
   
   //if using OCLC ILL
   if ($oclcILL == 'true') {
    $openILL = $illForm . $oclcNum;
    } else {
    $openILL = $illForm . $query;

    }
    
    //Request data from Availability API
   use OCLC\Auth\WSKey;
   use OCLC\User;
   use Guzzle\Http\Client;
   $wskey = new WSKey($key, $secret);
   $url = 'https://worldcat.org/circ/availability/sru/service?x-registryId=' . $institutionId . '&x-return-group-availability=true' . '&query=no:ocm' . $oclcNum;
   $user = new User($institutionId, $principleId, $principleIdNs);
   $options = array('user'=> $user);

   $authorizationHeader = $wskey->getHMACSignature('GET', $url, $options);
   
   $client = new Guzzle\Http\Client();
   $client->get('config/curl/' . CURLOPT_SSLVERSION, 3);
   $headers = array();
   $headers['Authorization'] = $authorizationHeader;
   $request = $client->createRequest('GET', $url, $headers);
   
   //use try in case no response / Exception from API
   try {
   		$response = $request->send();
        $xml = $response->getBody(TRUE);
        $xmlObj = simplexml_load_string($xml);
		if(isset($xmlObj->records->record->recordData->opacRecord->holdings->holding))  {
		    $institutions = array();
		    $availability = array();
		    $callnums = array();
		    $shelfs = array();
		    foreach ($xmlObj->records->record->recordData->opacRecord->holdings->holding as $holding) {
		      $institution = (string) $holding->nucCode;
		      $shelf = (string) $holding->shelvingLocation;
		      $callnum = (string) $holding->callNumber;
		      $instavailcount = array();
		      $datapoints = array();
		      foreach ($holding->circulations->circulation as $circulation) {
		          $institutions[] = $institution;
		          $shelfs[] = $shelf;
		          $callnums[] = $callnum;
		      	  $avail = (string) $circulation->availableNow->attributes();
			      $availability[] = $avail;
		          $barcode = $circulation->itemId;    
			      } 	          
        }
      
				 
		          $availarray = array();
		          $callavail = array();
		          $shelfarray = array();
		          $availarray = array_combine($institutions,$availability);
		          $callavail = array_combine($institutions, $callnums);
		          $shelfarray = array_combine($institutions, $shelfs);
		          $availmerged = array_merge_recursive($availarray, $callavail, $shelfarray);
		          $institutionHolding = (isset($availmerged[$institutionCode]))?$availmerged[$institutionCode]:'There is no '.$institutionCode.' holding';
		          $holdCount = $institutionHolding[0];
		          $institutionCall = $institutionHolding[1];
		          $institutionShelf = $institutionHolding[2];
		          
		          function in_array_any($n, $h) {
   					return !!array_intersect($n, $h);
				  }
          if ($institutionHolding[0] > 0) {
		        $holdurl = 'https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD';
		        //Write to log file
		        $current = "Place Hold: " . $holdurl ."\n";
          		$current .= file_get_contents($logfile);
          		file_put_contents($logfile, $current);
          		if ($onshelfHold == 'true') {
          		Header( 'Location: '. $holdurl  ) ;
          		}
          		else {
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
    				<p class="lead">This item is available in your library! <a href="'. $holdurl . '">Place a hold</a> or find it on the shelf:  <br /><strong>Call Number:</strong>  ' . $institutionCall . '  <br /><strong>Shelving Location:</strong>  ' . $institutionShelf . '</div><!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
    				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    				<!-- Include all compiled plugins (below), or include individual files as needed -->
    				<script src="../bootstrap/js/bootstrap.min.js"></script></body></html>';
    				}
		    }  elseif ($usepreferredLenders == 'true') {
		    		if (in_array_any($preferredLenders, $institutions)) {
		    			//Write to log file
		    				$prefholdurl = 'https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD';
		        			$current = "Preferred Lender Hold: " . $prefholdurl ."\n";
          					$current .= file_get_contents($logfile);
          					file_put_contents($logfile, $current);
		         		Header( 'Location: https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD' );
		         	} else {
		         		//Write to log file
		        			$current = "No Preferred Lender ILL Form: " . $openILL ."\n";
          					$current .= file_get_contents($logfile);
          					file_put_contents($logfile, $current);
    		        	//Header( 'Location: '. $illForm . '?rfe_dat=' . $oclcNum . '&rft.btitle=' . $bookTitle . '&rft_aulast=' . $authorLast . '&rft_aufirst=' . $authorFirst . '&rft_isbn=' . $isbn . '&rft_date=' . $pubDate  ) ;
    		        	Header( 'Location: '. $openILL  ) ;
    		        }
		    }  elseif ($usepreferredLenders == 'false') {
		          	if (!empty($institutions)) {
		          		//Write to log file
		    				$palshareurl = 'https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD';
		        			$current = "PALShare Hold: " . $palshareurl ."\n";
          					$current .= file_get_contents($logfile);
          					file_put_contents($logfile, $current);
		              	Header( 'Location: https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD' );
		             } else {
		             	//Write to log file
		        			$current = "No PALShare ILL: " . $openILL ."\n";
          					$current .= file_get_contents($logfile);
          					file_put_contents($logfile, $current);
    		        	Header( 'Location: '. $openILL  ) ;
    		        }
    			}
        
        
  		} else {
  			//Write to log file
		        $current = "Fallback ILL: " . $openILL ."\n";
          		$current .= file_get_contents($logfile);
          		file_put_contents($logfile, $current);
    		Header( 'Location: '. $openILL  ) ;
    }
    }  
    //catch errors (e.g., API is down) and forward request to ILL form
    catch (\Exception $e) {
    //Write to log file
		        $current = "API Failure ILL: " . $openILL ."\n";
          		$current .= file_get_contents($logfile);
          		file_put_contents($logfile, $current);
	Header( 'Location: '. $openILL  ) ;
    }
    //Handle Warnings
        function warning_handler($errno, $errstr) { 
	    Header( 'Location: '. $openILL  ) ;
    }
?>
