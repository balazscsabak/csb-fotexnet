<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/config/config.php');
require_once(__ROOT__.'/db/SQLiteConnection.php');
require_once(__ROOT__.'/db/PortChannels.php');

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
     * -
     * Tábla ellenőrzése, ha:
     *      - van tábla, program fut tovább
     *      - nincs tábla, létrehozzuk 
     */
    $PortChannels = new PortChannels($SQLiteConn);

    $tables = $PortChannels->getTablesList();

    if(count($tables) < 1){
        $PortChannels->createTable();
    } 

    // $PortChannels->showPrograms();
    // $PortChannels->showProgramsByChannelANdByDay('2020-10-17', 'TV2');
    // $PortChannels->showAvailableDaysByChannelId("RTL Klub");
}
?>

<?php include_once(__ROOT__.'/includes/header.php'); ?>

<!-- CONTENT -->
<div>
    <h1>Új Lekérdezés</h1>
    <h2>Kérem írja be a dátumot</h2>
    
    <div class="form-group row">
        <label for="date-picker" class="col-2 col-form-label">Date</label>
        <div class="col-10">
            <input class="form-control" type="date" value="2020-10-11" id="date-picker">
        </div>
    </div>
    
    <button type="button" id="start-query" class="btn btn-info">Lekérdezés</button>

    <div id="query-result"></div>
</div>

<?php include_once(__ROOT__.'/includes/footer.php'); ?>