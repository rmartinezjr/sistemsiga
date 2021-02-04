<?php
$cad="";
$cont=0;
foreach ($datos["nav"] as $item){
    $cad = $item["aliasn1"]." > ".$item["aliasn2"]." > ".$item['alias'];

}
foreach ($datos["complemento"] as $item2){
    $cad .= " > ".$item2["alias"];
}
?>
<p class="text-left lbl-navegacion"><?=$cad?></p>
