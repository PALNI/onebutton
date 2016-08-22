<?php
    //held by Goshen (Place PALShare Hold)
    //https://get.palni.org/ancilla/link.php?url_ver=Z39.88-2004&rfr_id=info%3Asid%2Fpalni.worldcat.org%3Aworldcat&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F166317545&rft_id=urn%3AISBN%3A9780152065454&rft.aulast=Winter&rft.aufirst=Jeanette&rft.btitle=Wangari%27s+trees+of+peace+%3A+a+true+story+from+Africa&rft.date=2008&rft.isbn=9780152065454&rft.place=Orlando+%5BFla.%5D&rft.pub=Harcourt&rft.edition=1st+ed.&rft.identifier=SB63.M22+W56+2008&req_id=info:rfa/oclc/institutions/59274&req_dat=&rfe_dat=166317545
    //held by Ancilla (Get It Page)
    //https://get.palni.org/ancilla/link.php?url_ver=Z39.88-2004&rfr_id=info%3Asid%2Fpalni.worldcat.org%3Aworldcat&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F738336589&rft_id=urn%3AISBN%3A9780525478812&rft.aulast=Green&rft.aufirst=John&rft.btitle=The+fault+in+our+stars&rft.date=2012&rft.isbn=9780525478812&rft.edition=First+edition.&rft.identifier=PZ7.G8233+Fau+2012&req_id=info:rfa/oclc/institutions/59274&req_dat=&rfe_dat=773128450
    //Carter & LoveCraft - no copies (ILL Form)
    //http://get.palni.org/ancilla/link.php?url_ver=Z39.88-2004&rfr_id=info%3Asid%2Fpalni.worldcat.org%3Aworldcat&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F914136934&rft_id=urn%3AISBN%3A9781250060891&rft.aulast=Howard&rft.aufirst=Jonathan&rft.auinitm=L&rft.btitle=Carter+%26+Lovecraft&rft.date=2015&rft.isbn=9781250060891&rft.edition=First+edition.&rft.identifier=PR6108.O928+C37+2015&req_id=info:rfa/oclc/institutions/59274&req_dat=&rfe_dat=914136934
   //short history of nearly everything - 3 copies (Place PALShare Hold)
   //https://get.palni.org/ancilla/link.php?url_ver=Z39.88-2004&rfr_id=info%3Asid%2Fpalni.worldcat.org%3Aworldcat&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F51900381&rft_id=urn%3AISBN%3A9780767908177&rft.aulast=Bryson&rft.aufirst=Bill&rft.btitle=A+short+history+of+nearly+everything&rft.date=2003&rft.isbn=9780767908177&rft.place=New+York&rft.pub=Broadway+Books&rft.edition=1st+ed.&rft.identifier=Q162+.B88+2003&req_id=info:rfa/oclc/institutions/59274&req_dat=&rfe_dat=51900381
   //OnlyatButler (ILL Form)
   //https://get.palni.org/ancilla/link.php?url_ver=Z39.88-2004&rfr_id=info%3Asid%2Fpalni.worldcat.org%3Aworldcat&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F14999560&rft.btitle=Catalog+of+former+students+not+Alumni+of+Butler+College.&rft.date=1900&rft.aucorp=Butler+University.&rft.place=Indianapolis&rft.pub=Hollenbeck+Press&rft.identifier=LD701+.B81a+1900&req_id=info:rfa/oclc/institutions/59274&req_dat=&rfe_dat=14999560
    //Multi-volume set 
    //https://get.palni.org/ancilla/link.php?url_ver=Z39.88-2004&rfr_id=info%3Asid%2Fancillacollegelibrary.worldcat.org%3Aworldcat&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F42199922&rft_id=urn%3AISBN%3A9780122270109&rft.aulast=Kurtz&rft.aufirst=Lester&rft.auinitm=R&rft.btitle=Encyclopedia+of+violence%2C+peace+%26+conflict&rft.date=1999&rft.isbn=9780122270109&rft.place=San+Diego&rft.pub=Academic+Press&rft.identifier=HM886+.E53+1999&req_id=info:rfa/oclc/institutions/929&req_dat=&rfe_dat=42199922
    $oclcNum = "";
    $bookTitle = "";
    $authorLast = "";
    $authorFirst = "";
    $isbn = "";
    $pubDate = "";
    
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
   require_once('../vendor/autoload.php');
   require('config.php');
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
			  	//foreach ($shelf as $innerArray) {
    				//$shelfs[] = (string) $innerArray;
    				//print_r($shelfs);
   				// }
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
		         // print_r($institutions);
		         //echo "<br />";
		        
			     // foreach ($availability as $indexnum => $key) {
			      //  $instavailcount[$institution] = $availability[$indexnum];
			        
			      //  } 
			    
			      //print_r($callnum);
			     // print_r($shelf);
		          
			      }  //  array_push($instavailcount, $callnum);
			         // array_push($instavailcount, $shelf);
			         //print_r($instavailcount);
			         //echo "<br />";
			        // $institutionHolding = $instavailcount[$institution];
			         
			          
        }//print_r($institutions);
       // echo "<br />";
        //print_r($callnums);
      //  echo "<br />";
       // print_r($shelfs);
      

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
		    }  elseif ($usepreferredLenders == 'true') {
		    		if (in_array_any($preferredLenders, $institutions)) {
		         		Header( 'Location: https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD' );
		         	} else {
    		        	Header( 'Location: '. $illForm . '?rfe_dat=' . $oclcNum . '&rft.btitle=' . $bookTitle . '&rft_aulast=' . $authorLast . '&rft_aufirst=' . $authorFirst . '&rft_isbn=' . $isbn . '&rft_date=' . $pubDate  ) ;
    		        }
		    }  elseif ($usepreferredLenders == 'false') {
		          	if (!empty($institutions)) {
		              	Header( 'Location: https://' . $institutionURL .'.on.worldcat.org/search?sortKey=LIBRARY_PLUS_RELEVANCE&databaseList=&queryString=' . $oclcNum . '&changedFacet=author&scope=&format=all&database=all#/oclc/' . $oclcNum . '/circ/hold/PLACE_HOLD' );
		             } else {
    		        	Header( 'Location: '. $illForm . '?rfe_dat=' . $oclcNum . '&rft.btitle=' . $bookTitle . '&rft_aulast=' . $authorLast . '&rft_aufirst=' . $authorFirst . '&rft_isbn=' . $isbn . '&rft_date=' . $pubDate  ) ;
    		        }
    			}
        
  		} else {
    		header( 'Location: '. $illForm . '?rfe_dat=' . $oclcNum . '&rft.btitle=' . $bookTitle . '&rft_aulast=' . $authorLast . '&rft_aufirst=' . $authorFirst . '&rft_isbn=' . $isbn . '&rft_date=' . $pubDate  ) ;
    }
?>