<?php

/**
 * The Localizer is able to return localized resources, depending
 * on the language chosen by the user.
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
abstract class Localizer {
    /**
     * Sets the language.
     * @param string $lang The ISO-language code of the language to use.
     */
    public abstract function setLanguage($lang);

    /**
     * Returns the last set language.
     * @return mixed The language's ISO-code
     */
    public abstract function getLanguage();

    /**
     * Returns the localized string that belongs to the given resource key.
     * @param string $key A unique key identifying the resource.
     * @param array $params An array containing arbitrary parameters. {0} in the
     *                      resource text will be replaced by the first parameter,
     *                      {1} with the second one, ...
     * @return mixed The localized string or null, if none.
     */
    public abstract function getString($key, $params = null);
}