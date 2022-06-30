<?php

$taskGroups = FALSE;

if($user->getFlagAdmin() == 'Y') {
	$taskGroups = TaskGroup::getByCondition("tskg_sem_id = ?", array($_SESSION["sem_id"]), array('tskg_order'));
} else {
	$taskGroups = TaskGroup::getByCondition("tskg_visible = 'Y' and tskg_sem_id = ?", array($_SESSION["sem_id"]), array('tskg_order'));
}

/**parses the description and hides eventual {hint} texts and unnecessary paragraphs
 * @param $description
 */
function parseDescriptionForTasks($description)
{
    //Remove hints
    $output = preg_replace('|\{hints\}(.+?)\{/hints\}|s','', $description);
    //Remove empty paragraphs with br
    $output = preg_replace('|\<p\>\<br\>\</p\>|s','',$output);
    //Remove empty divs with br
    $output = preg_replace('|\<div\>\<br\>\<\/div\>|s','',$output);
    //Remove the horizontal line
    $output = preg_replace('|\<hr\>|s','',$output);
    //Remove empty paragraphs
    for ($i=0;$i<20;$i++)
        $output = preg_replace('|\<p\>\</p\>|s','',$output);
    //Remove line breaks at the end
    $chunks = explode('<br>',$output);
    $output = "";
    for ($i = 0; $i < count($chunks)-1;$i++)
    {
        if ($i != 0)
            $output.="<br>";
        if (strlen(trim($chunks[$i])) > 0)
            $output.=$chunks[$i];
    }
    if (strlen(trim($chunks[count($chunks)-1])) > 0)
        $output.=$chunks[count($chunks)-1];
   // return $description;
    return $output;
}

