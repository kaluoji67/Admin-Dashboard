<?php

/**
 * Class representing a result, consisting of the head (column names)
 * and the body (result set).
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
class Result {
    private $columns;
    private $resultSet;

    /**
     * @param array $resultSet The result set.
     * @param array $columns The column names.
     */
    public function __construct($resultSet = array(), $columns = array()) {
        $this->resultSet = $resultSet;
        $this->columns = $columns;
    }

    /**
     * Returns the result set.
     * @return array The result set.
     */
    public function getResultSet() {
        return $this->resultSet;
    }

    /**
     * Returns the columns.
     * @return array The columns.
     */
    public function getColumns() {
        return $this->columns;
    }
}