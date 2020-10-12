<?php
require_once('../../db/SQLiteConnection.php');
require_once('../../db/PortChannels.php');

if(isset($_GET['day']) && isset($_GET['channelName'])){
    /**
     * DB connection
     */
    $SQLiteConn = (new SQLiteConnection())->connect();

    /**
     * Connection ellenőrzése
     */
    if ($SQLiteConn != null){
        /**
         * Új PortChannels instance
         */
        $PortChannels = new PortChannels($SQLiteConn);

        /**
         * Programok lekérdezése
         */
        $result = $PortChannels->showProgramsByChannelANdByDay($_GET['day'], $_GET['channelName']);
        echo json_encode($result);
    }

} else {
    http_response_code(404);
    die();
}

