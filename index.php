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
    <h1>Kérem válasszon csatornát</h1>
    <div class="channels row">
    <?php
    foreach (Config::CHANNELS_LIST as $channel) {
        $channelName = $channel['name'];
        echo '<div class="channel col-12 col-md-2" data-ch-name="'. $channelName .'">'. $channelName .'</div>';
    }
    ?>
    </div>

    <div class="days">
        <div id="days-selector-box">
            
        </div>
    </div>

    <table id="programs" class="table">
    </table>
</div>

<?php include_once(__ROOT__.'/includes/footer.php'); ?>