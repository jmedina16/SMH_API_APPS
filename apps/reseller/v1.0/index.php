<?php
/*
    SMH Reseller Plugin

    Actions:
	Verify			== A method to verify the parter parent has access.
	List/Get 		== Get a list of Children accounts for partner.
	Edit/Update 	== Modify a child account
	Delete			== Remove a child account
	Block/Suspend	== Disable portal/stream access for child account
	Create			== Add an additional Child account



$baseUrl = "http://10.5.20.22/index.php/api/reseller";

$req = array(
	'uri' => $_SERVER['REQUEST_URI'],
	'method' => $_SERVER['REQUEST_METHOD'],
	'querystr' => $_SERVER['QUERY_STRING']
);


*/

function method_time($x = null)
{
   #$time = time();

   #syslog(LOG_NOTICE,"Reseller -- time_start: ".$time);

   $stime = null;

   if (isset($x))
   {
	return $x - $stime;
   }
   else
   {
	$stime = time();
	syslog(LOG_NOTICE,"Reseller -- time_start: ".$stime);	
   }
}

function debug_output()
{
    if ($req['querystr'] !== "")
    {
	echo "<span>Req Uri: 	".$req['uri']."</span><br />";
	echo "<span>Req Method: ".$req['method']."</span><br />";
	echo "<span>Req QryStr:	".$req['querystr']."</span><br />";
    }
    else
    {
	echo "<span>No action provided!";
   }
   echo "<br /><br />";
   echo "";
}

// API Methods
function verify_ks($ks, $parent)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://mediaplatform.streamingmediahosting.com/api_v3/index.php?service=partner&action=getInfo");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array("ks"=>$ks));
    $output = curl_exec($ch);
    curl_close($ch);
	
	$resp = new simpleXMLElement($output);
	
	//syslog(LOG_NOTICE,"Reseller -- verify_ks-resp: ".$resp->result->id);
	
	if (isset($resp->result->id) && $resp->result->id == $parent)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function list_children($parent, $fmt)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.5.20.22/index.php/api/reseller/list.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array("partnerId"=>"$parent"));
    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);

    #syslog(LOG_NOTICE,'Reseller: list_children -- resp: '.print_r($result,true));

    if (isset($result['error']))
    {
	return_resp(array("Error" => $result['error']), $fmt);
    }
    else
    {
	return_resp($result, $fmt);
    }
}

function status_child($child,$stat,$fmt)
{
    //syslog(LOG_NOTICE,'Reseller -- status_child(child: '.$child.' status: '.$stat.')');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.5.20.22/index.php/api/reseller/status.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array("partnerId"=>"$child","status"=>"$stat"));
    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);

    #syslog(LOG_NOTICE,'Reseller: list_children -- resp: '.print_r($result,true));

    if (isset($result['error']))
    {
        return_resp(array("Error" => $result['error']), $fmt);
    }
    else
    {
        return_resp($result, $fmt);
    }
}

function delete_child($child, $fmt)
{
    #syslog(LOG_NOTICE,'Reseller -- delete_child(child: '.$child.')');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.5.20.22/index.php/api/reseller/delete.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array("partnerId"=>$child));
    $output = curl_exec($ch);
    curl_close($ch);

    #return_resp($output);

    $result = json_decode($output, true);

    #syslog(LOG_NOTICE,'Reseller: delete_child -- resp: '.print_r($result,true));

    if (isset($result['error']))
    {
        return_resp(array("Error" => $result['error']), $fmt);
    }
    else
    {
        return_resp($result, $fmt);
    }

}

function update_child($child, $data, $fmt)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.5.20.22/index.php/api/reseller/update.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 
	array(
		"partnerId"	=> $child,
		"busname"	=> $data['businessName'],
		"busdesc"	=> $data['businessDescription'],
		"ownname"	=> $data['ownerName'],
		"ownemail"	=> $data['ownerEmail']
	));
    $output = curl_exec($ch);
    curl_close($ch);

    #return_resp($output);

    $result = json_decode($output, true);

    #syslog(LOG_NOTICE,'Reseller: update_child -- resp: '.print_r($result,true));

    if (isset($result['error']))
    {
        return_resp(array("Error" => $result['error']), $fmt);
    }
    else
    {
        return_resp(array("result" => "success!"), $fmt);
    }

}

function create_child($parent, $data, $fmt)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.5.20.22/index.php/api/reseller/create.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 
	array(
		"parentId"	=> $parent,
		"busname"	=> $data['businessName'],
		"busdesc"	=> $data['businessDescription'],
		"ownname"	=> $data['ownerName'],
		"ownemail"	=> $data['ownerEmail']
	));
    $output = curl_exec($ch);
    curl_close($ch);

    #return_resp($output);

    $result = json_decode($output, true);

    syslog(LOG_NOTICE,'Reseller: create_child -- resp: '.print_r($result,true));

    if (isset($result['error']))
    {
        return_resp(array("Error" => $result['error']), $fmt);
    }
    else
    {
        return_resp(array("result" => "success!"), $fmt);
    }

}
// Response

function convertToXml($myArray)
{
    // prepare resp.
    $xml = new SimpleXMLElement('<result></result>');

    // format/fix resp.
    
    // detect multi-dimensional array
    if (isset($myArray[0]) && is_array($myArray[0]))
    {	// multi-dimensional array detected
	foreach($myArray as $arr)
	{   // another dimension?
	    if (isset($arr[0]) && is_array($arr[0]))
	    {

	    }
	    else // only a single
	    {
   		array_walk_recursive(array_flip($arr), array ($xml, 'addChild'));
            }
	}
    }
    else
    {
	array_walk_recursive(array_flip($myArray), array ($xml, 'addChild'));
    }

    // return resp as xml.
    return $xml->asXML();
}

function return_resp($resp,$fmt = 'json')
{
    // Set content type
    header('Content-type: application/'.$fmt);

    #$return_time = time();

    syslog(LOG_NOTICE,"Reseller -- resp_format: ".$fmt);

    #$endtime = method_time(time());

    #$resp['time'] = $endtime;

    syslog(LOG_NOTICE,"Reseller -- resp: ".print_r($resp,true));

    // determine response format
    if ($fmt == 'xml')
    {
	echo convertToXml($resp);
    }
    else // json
    {
	echo json_encode($resp);
    }
 
}

// timer
method_time();

// start

$baseUrl = "http://10.5.20.22/index.php/api/reseller";

$req = array(
        'uri' => $_SERVER['REQUEST_URI'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'querystr' => $_SERVER['QUERY_STRING']
);

// Setup req methods



if ($req['method'] == "GET")
{
    parse_str($req['querystr'], $reqarr);
    #var_dump($reqarr);
}
elseif ($req['method'] == "POST")
{
	foreach($_POST as $key=>$value)
	{
		$reqarr[$key] = $value;
	}
	
	if (isset($_GET['action']))
	{
		$reqarr['action'] = $_GET['action'];
	}
	if (isset($_GET['format']))
	{
		$reqarr['format'] = $_GET['format'];
	}
	else
	{
		$reqarr['format'] = 'json';
	}
	
	//syslog(LOG_NOTICE,'Reseller -- setup: '.print_r($reqarr,true));
}
else
{
    return_resp(array("Error" => "Action not supported!"), $reqarr['format']);
    die;
}

// Check for params
if ($reqarr['ks'])
{
	if (verify_ks($reqarr['ks'], $reqarr['parentId']))
	{
	
		if (!$reqarr['format'])
		{
			
			$reqarr['format'] = 'json';
		}
	
	
		if (!$reqarr['action'])
		{
		   return_resp(array("Error" => " No action provided!"), $reqarr['format']);
		   die;
		}
		else
		{
		  if (!$reqarr['parentId'] || !$reqarr['ks'])
		  {
			if (!$reqarr['parentId']) {
				return_resp(array("Error" => "parentId required!"), $reqarr['format']);
				die;
			}
			if (!$reqarr['ks']) {
                                return_resp(array("Error" => "Session StSession String (ks)) required!"), $reqarr['format']);
                                die;	
			}
		  }
		  else
		  {
			  // now what?

			if ($reqarr['action'] == "list")
			{
				// return partner list
				list_children($reqarr['parentId'], $reqarr['format']);
				die;
			}

			if ($reqarr['action'] == "status")
			{
			    if ($reqarr['childId'] && $reqarr['status'])
                            {
				status_child($reqarr['childId'],$reqarr['status'],$reqarr['format']);
				die;
			    }
			    else
			    {
				if (!$reqarr['childId']) {
	    			    return_resp(array("Error" => "ChildId required!"), $reqarr['format']);
				    die;
				}
                                if (!$reqarr['status']) {
                                    return_resp(array("Error" => "Child status required!"), $reqarr['format']);
				    die;
                                }

			    }
			}

			if ($reqarr['action'] == "update")
			{
				$data;
				foreach ($reqarr as $key=>$value)
				{
					if ($key !== 'action' || 'parentId' || 'ks')
					{
						$data[$key] = $value;
					}
				}
				
				if (!empty($data))
				{
					update_child($reqarr['childId'], $data, $reqarr['format']);
					die;
				}
				else
				{
					return_resp(array("Error" => "No parameters provided!"), $reqarr['format']);
				}
			}

			if ($reqarr['action'] == "create")
			{
				$data;
				foreach ($reqarr as $key=>$value)
				{
					if ($key !== 'action' || 'parentId' || 'ks')
					{
						$data[$key] = $value;
					}
				}
				
				if (!empty($data))
				{
					create_child($reqarr['parentId'], $data, $reqarr['format']);
					die;
				}
				else
				{
					return_resp(array("Error" => "No parameters provided!"), $reqarr['format']);
				}
			}

			if ($reqarr["action"] == "delete")
			{
			    if ($reqarr['childId']) 
			    {
				delete_child($reqarr['childId'], $reqarr['format']);
				die;
			    }
			    else
			    {
				return_resp(array("Error" => "ChildId required!"), $reqarr['format']);
				die;
                            }
			}
			// Error, method not supported
			return_resp(array("Error" => "Action not supported!"), $reqarr['format']);
			die;
		  } 
		}
	}
	return_resp(array("Error" => "You are not authorized to make this request!"), $reqarr['format']);
	die;
}

return_resp(array("Error" => "Session string (ks) required!"), $reqarr['format']);
?>
