<?php
require_once('../../db/SQLiteConnection.php');
require_once('../../db/PortChannels.php');

if(isset($_GET['day'])){
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
         * port.hu lekérdezés indítása, nap alapján
         */
        try{
            $result = $PortChannels->getPrograms($_GET['day']);
            echo json_encode(["success"=>true]);
        } catch (Exception $e) {
            echo json_encode(["success"=>false]);
        }
    }

} else {
    http_response_code(404);
    die();
}

