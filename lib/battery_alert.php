<?php
function checkBatteryAndNotify($conn, $token, $chatId) {
    $datenow = date('Y-m-d H:i:s');
    $futureDate = date('Y-m-d H:i:s', strtotime("+180 days", strtotime($datenow)));

    // Fetch battery records based on Datex2
    $sql = "
        SELECT AssetNo, Asset, Datex2, DNAME,Datex, MaintainanceGroup
        FROM Batt_alert2 
        WHERE Datex2 >= '$futureDate' 
          AND Datex2 <= '$futureDate'
    ";

    $stmt = sqlsrv_query($conn, $sql, [], ["Scrollable" => 'static']);
    $row_count = sqlsrv_num_rows($stmt);

    if ($row_count === false || $row_count == 0) {
        sendTelegramMessage($token, $chatId, "\xF0\x9F\x94\x8B<strong>Today [$datenow] No batteries need replacement.</strong>\xF0\x9F\x94\x8B");
        return;
    }

    // Initial message before sending the rows
    sendTelegramMessage($token, $chatId, "\xF0\x9F\x9A\xA8<strong>Today [$datenow] Batteries to replace: $row_count item(s)</strong>\xF0\x9F\x9A\xA8\n");

    $curGroup = '';
    $n = 0;

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Determine if the group has changed
        $maintGroup = iconv('TIS-620', 'UTF-8//IGNORE', $row['MaintainanceGroup']);
        
        // If the maintenance group is different, reset the counter
        if ($maintGroup !== $curGroup) {
            $curGroup = $maintGroup;
            $n = 1;
        } else {
            $n++;
        }

        // Build the message for the current battery record
        $dname = iconv('TIS-620', 'UTF-8//IGNORE', $row['DNAME']);
        $asset = iconv('TIS-620', 'UTF-8//IGNORE', $row['Asset']);
        $datex2 = $row['Datex2']->format('Y-m-d');
        $msg  = "---------------\xF0\x9F\x9A\xA8\xF0\x9F\x94\x8B<strong>Please Replace Batterries.</strong>\xF0\x9F\x94\x8B\xF0\x9F\x9A\xA8---------------\n";
        $msg .= "$n. <strong>Name :</strong> $asset\n";
        $msg .= "    \xF0\x9F\x93\x91<strong>AssetNo. :</strong> {$row['AssetNo']}\n";
        $msg .= "    \xF0\x9F\x93\x85<strong>Expired date :</strong> $datex2 \n";
        $msg .= "    \xF0\x9F\x93\x8C<strong>Location : </strong> $dname\n";
        $msg .= "    \xF0\x9F\x95\x90<strong>180 Day left.</strong>\n";
      
        // Send the message for the current row (bubble)
        sendTelegramMessage($token, $chatId, $msg);
    }
}
?>