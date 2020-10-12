<?php
if (!defined('__ROOT__')) {
    define('__ROOT__', dirname(dirname(__FILE__)));
}
include_once(__ROOT__."/config/config.php");

class PortChannels {
    /**
     * PDO instance
     */
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Adatbázisban lévő táblák lekérdezése
     */
    public function getTablesList() {
        $stmt = $this->pdo->query("SELECT name
                                   FROM sqlite_master
                                   WHERE type = 'table'
                                   ORDER BY name");

        $tables = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tables[] = $row['name'];
        }

        return $tables;
    }

    /**
     * Szükséges tábla létrehozása
     */
    public function createTable() {
        /**
        * 'programs' tábla a műsorok tárolására
        */
        $table =   'CREATE TABLE IF NOT EXISTS programs (
                     program_id VARCHAR (255) PRIMARY KEY,
                     channel_name VARCHAR (255),
                     start_date TEXT,
                     date TEXT,
                     program_title  VARCHAR (255) NOT NULL,
                     program_desc  VARCHAR (255) NOT NULL,
                     age_limit VARCHAR (255)
                    )';

        // SQL szript futtatása
        $this->pdo->exec($table);       
    }

    /**
     * TV program mentése az adatbázisba.
     * Vizsgáljuk az azonosító alapján szerepel e már 
     * az adatbázisban, ha:
     *      - szerepel: nem történik semmi
     *      - nem szerepel: beszúrjuk a TV programot
     */
    public function saveProgramIfNotExists($programId, $channelName, $programTitle, $programStartDate, $programAgeLimit, $programDesc, $date){
        try {
            // SQL szkript
            $sql = 'SELECT * 
                    FROM programs
                    WHERE program_id = ?';
            
            // Előkészítés
            $stmt = $this->pdo->prepare($sql);

            // Érték átadása, futtatás
            $stmt->execute([$programId]);

            $program = [];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $program = $row;
            }

            /**
             * Eredmény vizsgálata:
             *      ha kisebb mint 1 => nincs az adatbázisban => mentés
             */
            if(count($program) < 1){
                // SQL szkript
                $sql2 = "INSERT INTO programs(program_id, channel_name, start_date, date, program_title, program_desc, age_limit) 
                        VALUES(?, ?, ?, ?, ?, ?, ?)";

                // Előkészítés
                $stmt2 = $this->pdo->prepare($sql2);

                // Érték átadása, futtatás
                $stmt2->execute([$programId, $channelName, $programStartDate, $date, $programTitle, $programDesc, $programAgeLimit]);

            } 
        } catch (Exception $e) {
            echo "Hiba a program mentése során";
        }
    }

    /**
     * Programok lekérdezése.
     * Dátum alapértelmezetten a mai dátum (YYYY-mm-dd).
     * Ciklussal végigmegyünk a csatornákon 
     *  - port.hu API lekérdezése a csatorna ID alapján
     *  - vizsgáljuk a csatornához tartozo TV programokat 
     * 
     * @param date - dátum, amelyik nap műsorát lészeretnénk kérdezni
     *             - formátum, pl: 2020-10-05
     */
    public function getPrograms($date = null){    
        $getDate = isset($date) ? $date : date("Y-m-d");

        foreach(Config::CHANNELS_LIST as $channel){
            // URL generálása
            $url = Config::PORT_API_URL . $channel["id"] . '&i_datetime_from='. $getDate . '&i_datetime_to=' . $getDate;
            
            // GET request
            $res = file_get_contents($url);
            
            // decode JSON
            $data = json_decode($res);

            foreach($data as $day) {
                $channelName = $channel["name"];
                if(count($day->channels) > 0){
                    $programs = $day->channels[0]->programs;
                
                    foreach($programs as $p){
                        $programId = $p->id;
                        $programTitle = $p->title;
                        $programStartDate = $p->start_datetime;
                        $programAgeLimit = $p->restriction->age_limit;
                        $programDesc = $p->short_description;
                        $this->saveProgramIfNotExists($programId, $channelName, $programTitle, $programStartDate, $programAgeLimit, $programDesc, $getDate);
                    }
                } else {
                    throw new Exception('No data');
                }
            }
        }
    }

    /**
     * Lekérdezi mely dátumok lettek mentve az adatbáziba,
     * a különböző csatorna nevek alapján.
     */
    public function showAvailableDaysByChannelId($channelName = null){
                
        $stmt = $this->pdo->query("SELECT date
                                    FROM programs
                                    WHERE channel_name = '". $channelName . "'
                                    GROUP BY date");

        $dates = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $dates[] = $row;
        }
 
        return $dates;
    }

    /**
     * Egy megadott naphoz tartozó műsor lista lekérdezése, a csatorna neve alapján.
     * Két ISO dátumot tárol külön változókban. 
     * Az egyik a megadott dátum pl 2020.10.11 => 2020.10.11 00:00:00.
     * A másik pedig a megadott dátum plusz hozzáadva egy teljes nap.
     * A lekérdezés a két dátum közti műsorokat adja vissza.
     */
    public function showProgramsByChannelANdByDay($day, $channelName=null){
        
        // Original
        $datetime = new DateTime($day . " 00:00:00");
        $date = $datetime->format(DateTime::ATOM); // Updated ISO8601 

        // Original + 1 nap
        $datetime2 = new DateTime($date);
        $datetime2->modify('+1 day');
        $date2 = $datetime2->format(DateTime::ATOM); // Updated ISO8601 
        
        $stmt = $this->pdo->query("SELECT * FROM programs
                                    WHERE channel_name = '". $channelName . "' AND
                                    start_date BETWEEN '" . $date . "' AND '" . $date2 . "'");

        $programs = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $programs[] = $row;
        }
 
        return $programs;
    }
}

?>