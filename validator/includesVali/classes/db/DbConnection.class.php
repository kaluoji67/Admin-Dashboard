<?php

require_once __DIR__ . '/Result.class.php';

/**
 * Class DbConnection
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
abstract class DbConnection {
    /**
     * Returns a string describing the type.
     * @return mixed The type.
     */
    public abstract function getType();

    /**
     * Executes the given query. The n'th questionmark within the query
     * is replaced with the escaped n'th parameter.
     * @param $query A valid SQL query
     * @param null $params An array of parameters
     * @return bool false, if the query execution failed.
     */
    public abstract function queryWithParams($query, $params = null);

    public abstract function query($query);

    /**
     * Returns the id of the last inserted row.
     * For some database drivers (e.g. PostgreSQL), this may not return
     * any useful information.
     * @return string The row id.
     */
    public abstract function lastInsertId();

    /**
     * Returns the error message associated with the last operation.
     * @return string The error message.
     */
    public abstract function getErrorText();

    /**
     * Returns the whole result set.
     * @return array The result set.
     */
    public abstract function fetchAll();

    /**
     * Returns the next result row.
     * @return array|bool The current row, or false.
     */
    public abstract function fetch();

    /**
     * Returns the result of the last query.
     * @return Result Result, consisting of header and the whole result set.
     */
    public abstract function result();
}