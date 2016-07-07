<?php
require_once('lib/Mobile_Detect.php');
function colorInput($id, $r = 128, $g = 128, $b = 128) {
$mobile = new Mobile_Detect;
$isMobile = $mobile->isMobile();
$values = array('red' => $r, 'green' => $g, 'blue' => $b);
ob_start();?><div class="colorInput"><?php
$components = array('red', 'green', 'blue');
for($i = 0; $i < 3; ++$i) {
	$component = $components[$i];
?><div class="color"><div class="component"><div <?php if(!$isMobile) { ?>onclick="document.getElementById('<?php echo $id.'-'.$component;?>').select();"<?php };?> class="border"><div class="downbar <?php echo $component;?>" <?php if($isMobile) { echo 'ontouchend="ColorInputChangeTouchedUnit(event);"'; } else { echo 'onclick="ColorInputChangeUnit(event);"'; } ?>><div class="upbar"></div></div></div></div><div class="input"><input size="5" id="<?php echo $id.'-'.$component;?>" name="<?php echo $id.'-'.$component;?>" type="text" value="<?php echo $values[$component];?>" onkeyup="ColorInputChangeInput(this);"/></div>
</div><?php };?></div><script type="text/javascript"><!--
ColorInputLoad('<?php echo $id;?>');
--></script><?php
$colorInputHtml = ob_get_contents();
ob_end_clean();
return $colorInputHtml;
};
?>