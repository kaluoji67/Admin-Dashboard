<?php

require_once __DIR__ . '/PDODbConnection.class.php';

/**
 * Class handling connections to MySQL- and MariaDB-databases.
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
class MariaDBPDOConnection extends PDODbConnection {
    const TYPE = "mysql";

    /**
     * @param $hostname The host to connect to.
     * @param $username The username.
     * @param $password The password.
     * @param $dbname The database to use.
     */
    public function __construct($hostname, $username, $password, $dbname) {
        parent::__construct(self::TYPE, "mysql:host=$hostname;dbname=$dbname", $username, $password);
    }
}