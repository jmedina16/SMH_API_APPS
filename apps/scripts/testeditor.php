<HTML>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
    </HEAD>
    <BODY>
        <?php
        require_once('../../smh_application/lib/KalturaClient.php');
        // CHANGE THE FOLLOWING PARAMS TO YOUR PARTNER AND APPLICATION INFORMATION:
        // Replace the 'xxxxx' with your partner values.
        $userId = "jmedinaktest@gmail.com";
        $partnerId = "10012";
        $adminsecret = "1a5415b756a0abc286aa1b0bb965f732";
        $host = "http://mediaplatform.streamingmediahosting.com";
        $mix_name = "My Mix";

        $config = new KalturaConfiguration($partnerId);
        $config->serviceUrl = $host;
        $client = new KalturaClient($config);
        $type = null;
        $expiry = null;
        $privileges = null;
        $type = KalturaSessionType::ADMIN;
        $sessionId = $client->session->start($adminsecret, $userId, $type, $partnerId, $expiry, $privileges);
        $client->setKS($sessionId);

        // create the filter
        $filter = new KalturaMediaEntryFilter();
        // .... that are admin-tagged for this purpose (i.e. for the remix)
        $filter->orderBy = '-createdAt';
        $filter->mediaTypeIn = '1';
        $filter->statusIn = '-1,-2,0,1,2,7,4';
        $pager = new KalturaFilterPager();
        $pager->pageSize = 5;

        // list entries that are returned with the filter defined above
        $filteredListResult = $client->baseEntry->listAction($filter, $pager);
        $mix = new KalturaMixEntry();
        $mix->name = $mix_name;
        $mix->type = KalturaEntryType::MIX;
        $mix->editorType = KalturaEditorType::SIMPLE;
        $results = $client->mixing->add($mix);
        $mix_id = $results->id;
        //print_r($filteredListResult);
        echo '<b>Your new mix ID is </b>"' . $mix_id . '"<br>';
//
//        // add entries to roughcut defined as $mix_id
//        echo '<b>These entries were added to the mix - </b><br>';
//        foreach ($filteredListResult->objects as $entry) {
//            $mixEntryId = $mix_id;
//            $mediaEntryId = $entry->id;
//            $results = $client-> mixing ->appendMediaEntry($mixEntryId, $mediaEntryId);
//            echo $entry->id . '-' . $entry->name . '<br>';
//        }

        $flashVars = array();
        $flashVars["partnerId"] = $partnerId;
        $flashVars["subpId"] = $partnerId . '00';
        $flashVars["uid"] = $userId;
        $flashVars["ks"] = $sessionId;
        $flashVars["kshowId"] = "entry-" . $mix_id;
        $flashVars["entryId"] = $mix_id;
        $flashVars["backF"] = "onSimpleEditorBackClick";
        $flashVars["saveF"] = "onSimpleEditorSaveClick";
        $flashVars["quick_edit"] = 0;
        
        ?>	   
        <script type="text/javascript">
            var params = {
                allowScriptAccess: "always",
                allowNetworking: "all",
                wmode: "opaque"
            };
            var flashVars = <?php echo json_encode($flashVars); ?>;
            swfobject.embedSWF("http://mediaplatform.streamingmediahosting.com/kcw/ui_conf_id/603", "kse", "890", "690", "9.0.0", false, flashVars, params);
        </script>

        <script type="text/javascript">
       
            function onSimpleEditorBackClick(isModified) {
                alert('onSimpleEditorBackClick');
            }
       
            function onSimpleEditorSaveClick() {
                alert('onSimpleEditorSaveClick');
            }
       
        </script>

        <div id="kse"></div>
    </BODY>
</HTML>