<?php
if (!defined('__ROOT__')) {
    define('__ROOT__', dirname(dirname(__FILE__)));
}

class Config {
    /**
     * DB elérési útvonala
    */
    const PATH_TO_SQLITE_DB = __ROOT__."/db/portchannels.db";

    /**
     * port.hu API az adatok lekéréséhez
    */
    const PORT_API_URL = 'https://port.hu/tvapi?channel_id=';

    /**
     * Csatornák listája, azonosítóval együtt
     */
    const CHANNELS_LIST = [["name" => "RTL Klub", "id" => "tvchannel-5" ],
                           ["name" => "TV2", "id" => "tvchannel-3" ],
                           ["name" => "Viasat 3", "id" => "tvchannel-21" ],
                           ["name" => "Duna Televízió", "id" => "tvchannel-6" ],
                           ["name" => "Duna World", "id" => "tvchannel-103" ]];
}

?>