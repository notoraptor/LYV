<?php
require_once('colorInput.php');
require_once('yvl.php');
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Personnalisation du logo LYV.</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="style.css"/>
	<link rel="stylesheet" href="colorInput.css"/>
	<script type="text/javascript" src="colorInput.js"></script>
</head>
<body>
<?php
class Message {
	public $errors = array();
	public $attentions = array();
	public $success = array();
	public function addError($message) { $this->errors[] = $message; }
	public function addSuccess($message) { $this->success[] = $message; }
	public function printAll() {
		if(!empty($this->errors)) echo '<div class="error">'.implode('</div><div class="error">', $this->errors).'</div>';
		if(!empty($this->attentions)) echo '<div class="attention">'.implode('</div><div class="attention">', $this->attentions).'</div>';
		if(!empty($this->success)) echo '<div class="success">'.implode('</div><div class="success">', $this->success).'</div>';
	}
}
function s_post($name, $or = false) {
	return isset($_POST[$name]) ? trim(strip_tags($_POST[$name])) : $or;
}
$message = new Message();
$success = false;
$identifiant = preg_replace('/[^0-9]+/','',microtime()."");
if(!empty($_POST)) {
	$alphaDegres = s_post('alpha');
	$h = s_post('h');
	$l = s_post('l');
	if(!Utils::isNatural($alphaDegres)) $message->addError("L'angle des bras du Y doit être entier naturel !");
	else if(($alphaDegres = intval($alphaDegres)) > 63) $message->addError("L'angle des bras du Y doit être compris entre 0 et 63 degrés inclus !");
	else if(!Utils::isNatural($h)) $message->addError("L'épaisseur des traits des lettres doit être un entier naturel !");
	else if(!Utils::isNatural($l)) $message->addError("La hauteur de l'image doit être un entier naturel !");
	else try {
		$drawer = new YVL();
		$drawer->alphaDegres = $alphaDegres;
		$drawer->h = intval($h);
		$drawer->l = intval($l);
		$drawer->colorY = new Color(s_post('cy-red'), s_post('cy-green'), s_post('cy-blue'));
		$drawer->colorV = new Color(s_post('cv-red'), s_post('cv-green'), s_post('cv-blue'));
		$drawer->colorLeft = new Color(s_post('cl-red'), s_post('cl-green'), s_post('cl-blue'));
		$drawer->colorRight = new Color(s_post('cr-red'), s_post('cr-green'), s_post('cr-blue'));
		$drawer->name = $identifiant;
		$_SESSION['drawer'] = $drawer;
		$message->addSuccess("Voici votre image (zoom à 400 pixels de large) !");
		$success = true;
	} catch(Exception $e) {
		$message->addError($e->getMessage());
	};
};
?>
<div class="messages"><?php $message->printAll(); ?></div>
<?php if($success) { ?>
<div class="image-infos">
	<div class="image"><img src="svg.php" style="display : block; width : 400px; margin : auto;"/></div>
	<div class="liens-images">
	<h1 class="titre">Téléchargez l'image</h1>
	<h2 class="svg"><a target="_blank" href="logo-<?php echo $identifiant;?>.svg">Qualité maximale (image vectorielle SVG)</a></h2>
	<h2 class="png"><a target="_blank" href="logo-<?php echo $identifiant;?>.png">Image (format PNG)</a></h2>
	<h2 class="jpeg"><a target="_blank" href="logo-<?php echo $identifiant;?>.jpg">Photo (format JPEG)</a></h2>
	</div>
</div>
<?php };
$preAlpha = s_post('alpha', 40);
$preH = s_post('h', 25);
$preL = s_post('l', 1600);
$cyr = s_post('cy-red', 107);
$cyg = s_post('cy-green', 150);
$cyb = s_post('cy-blue', 0);
$cvr = s_post('cv-red', 122);
$cvg = s_post('cv-green', 170);
$cvb = s_post('cv-blue', 0);
$clr = s_post('cl-red', 182);
$clg = s_post('cl-green', 255);
$clb = s_post('cl-blue', 0);
$crr = s_post('cr-red', 157);
$crg = s_post('cr-green', 219);
$crb = s_post('cr-blue', 0);
?>
<h1 class="titre-formulaire">Crééz votre logo !</h1>
<h3 style="margin-bottom : 30px;"><a href=".">Remettre les valeurs par défaut</a></h3>
<form method="post" action=".">
	<div class="soumission"><input type="submit" value="créer l'image !"/></div>
	<div class="formulaire">
		<div class="entree">
			<div class="label"><label for="alpha">Angle des bras du Y par rapport à l'horizontale (0 à 63 degrés)</label></div>
			<div class="champ"><select name="alpha" id="alpha"><?php for($i = 0; $i <= 63; ++$i) echo '<option value="'.$i.'"'.($i == $preAlpha ? ' selected="selected"' : '').'>'.$i.'</option>';;?></select></div>
		</div>
		<div class="entree">
			<div class="label"><label for="h">Épaisseur des traits des lettres (en pixels)</label></div>
			<div class="champ"><input name="h" id="h" type="text" value="<?php echo $preH;?>"/></div>
		</div>
		<div class="entree">
			<div class="label"><label for="l">Hauteur de l'image (en pixels)</label></div>
			<div class="champ"><input name="l" id="l" type="text" value="<?php echo $preL;?>"/></div>
		</div>
		<div class="entree">
			<div class="label"><label for="cy-red">Couleur du Y</label></div>
			<div class="champ"><?php echo colorInput('cy',$cyr,$cyg,$cyb);?></div>
		</div>
		<div class="entree">
			<div class="label"><label for="cv-red">Couleur du V</label></div>
			<div class="champ"><?php echo colorInput('cv',$cvr,$cvg,$cvb);?></div>
		</div>
		<div class="entree">
			<div class="label"><label for="cl-red">Couleur de l'intérieur gauche</label></div>
			<div class="champ"><?php echo colorInput('cl',$clr,$clg,$clb);?></div>
		</div>
		<div class="entree">
			<div class="label"><label for="cr-red">Couleur de l'intérieur droit</label></div>
			<div class="champ"><?php echo colorInput('cr',$crr,$crg,$crb);?></div>
		</div>
	</div>
	<div class="soumission"><input type="submit" value="créer l'image !"/></div>
</form>
</body>
</html>