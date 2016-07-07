<?php
class Utils {
	public static function isNatural($string) {
		return preg_match('/^[0-9]+$/', $string);
	}
}
class Point {
	public $x;
	public $y;
	public function __construct($x = 0,$y = 0) {
		$this->x = (int)round($x);
		$this->y = (int)round($y);
	}
	public function changeBase($x, $y) {
		$this->x -= $x;
		$this->y = $y - $this->y;
	}
	public function __toString() {
		return $this->x.",".$this->y;
	}
}
class Color {
	public $r;
	public $g;
	public $b;
	public function __construct($r = 0, $g = 0, $b = 0, $colorName = "couleur") {
		if(is_float($r)) $r = (int)round($r);
		if(is_float($g)) $g = (int)round($g);
		if(is_float($b)) $b = (int)round($b);
		$r = $r."";
		$g = $g."";
		$b = $b."";
		if(!Utils::isNatural($r)) throw new Exception($colorName." : la composante rouge doit être un entier naturel.");
		if(!Utils::isNatural($g)) throw new Exception($colorName." : la composante verte doit être un entier naturel.");
		if(!Utils::isNatural($b)) throw new Exception($colorName." : la composante bleue doit être un entier naturel.");
		$r = intval($r);
		$g = intval($g);
		$b = intval($b);
		if($r > 255) throw new Exception($colorName." : la composante rouge doit être comprise entre 0 et 255 inclus.");
		if($g > 255) throw new Exception($colorName." : la composante verte doit être comprise entre 0 et 255 inclus.");
		if($b > 255) throw new Exception($colorName." : la composante bleue doit être comprise entre 0 et 255 inclus.");
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}
	public function __toString() {
		return "rgb(".$this->r.",".$this->g.",".$this->b.")";
	}
}
class YVL {
	// Paramètres.
	public $alphaDegres = 40;
	public $h = 25;
	public $l = 1600;
	// Paramètres de coloration.
	public $colorY = null;
	public $colorV = null;
	public $colorLeft = null;
	public $colorRight = null;
	// Autres paramètres.
	public $name = '';
	// Points.
	private $A = null;
	private $B = null;
	private $C = null;
	private $D = null;
	private $H = null;
	private $E = null;
	private $G = null;
	private $F = null;
	private $I = null;
	private $J = null;
	private $K = null;
	private $J1 = null;
	private $J2 = null;
	// Informations supplémentaires.
	private $xmax = 0;
	private $ymax = 0;
	// Constructeur (pour les couleurs).
	public function __construct() {
		$this->colorY = new Color(107,150,0);
		$this->colorV = new Color(122,170,0);
		$this->colorLeft = new Color(182,255,0);
		$this->colorRight = new Color(157,219,0);
	}
	// Fonction de calcul des coordonnées des points.
	private function computePoints() {
		// Calcul des paramètres de base.
		$alpha = $this->alphaDegres*pi()/180;
		$h = $this->h;
		$l = $this->l;
		$a = $l/2.17;
		$rl2a2 =  sqrt($l*$l + $a*$a);
		$tanAlpha = tan($alpha);
		$cosAlpha = cos($alpha);
		// Calcul des coordonnées des points de l'image.
		$A = new Point(0,$l);
		$B = new Point($a, $l - $a*$tanAlpha);
		$C = new Point(2*$a,$l);
		$D = new Point($a,0);
		$H = new Point(	2*$a - $h*(($a/$cosAlpha) + $rl2a2)/($l - $a*$tanAlpha),
						($h*$rl2a2/$a) + $l - $h*$l*(($a/$cosAlpha) + $rl2a2)/($a*($l - $a*$tanAlpha)));
		$E = new Point(2*$a - $H->x, $H->y);
		$G = new Point($a + $h/2.0, $l - $tanAlpha*((2*$a - $h)/2.0) - $h/$cosAlpha);
		$F = new Point($a - $h/2.0, $G->y);
		$I = new Point($a + $h/2.0, $h*($l/2.0 + $rl2a2)/$a);
		$J = new Point($a, $h*$rl2a2/$a);
		$K = new Point($a - $h/2.0, $I->y);
		$J1 = new Point($I->x,$J->y);
		$J2 = new Point($K->x,$J->y);
		// Changement de l'origine du repère : du repère mathématique habituel vers le repère informatique.
		$this->xmax = 0;
		$this->ymax = 0;
		foreach(array($A,$B,$C,$D,$H,$E,$G,$F,$I,$J,$K,$J1,$J2) as $point) {
			if($this->ymax < $point->y) $this->ymax = $point->y;
			if($this->xmax < $point->x) $this->xmax = $point->x;
		};
		$A->changeBase(0, $this->ymax);
		$B->changeBase(0, $this->ymax);
		$C->changeBase(0, $this->ymax);
		$D->changeBase(0, $this->ymax);
		$H->changeBase(0, $this->ymax);
		$E->changeBase(0, $this->ymax);
		$G->changeBase(0, $this->ymax);
		$F->changeBase(0, $this->ymax);
		$I->changeBase(0, $this->ymax);
		$J->changeBase(0, $this->ymax);
		$K->changeBase(0, $this->ymax);
		$J1->changeBase(0, $this->ymax);
		$J2->changeBase(0, $this->ymax);
		// Mise en place des points calculés dans l'instance de la classe.
		$this->A = $A;
		$this->B = $B;
		$this->C = $C;
		$this->D = $D;
		$this->H = $H;
		$this->E = $E;
		$this->G = $G;
		$this->F = $F;
		$this->I = $I;
		$this->J = $J;
		$this->K = $K;
		$this->J1 = $J1;
		$this->J2 = $J2;
	}
	// Fonction de dessin au format SVG.
	public function svg() {
		// Calcul des coordonnées des points.
		$this->computePoints();
		// Récupération des points.
		$A = $this->A;
		$B = $this->B;
		$C = $this->C;
		$D = $this->D;
		$H = $this->H;
		$E = $this->E;
		$G = $this->G;
		$F = $this->F;
		$I = $this->I;
		$J = $this->J;
		$K = $this->K;
		$J1 = $this->J1;
		$J2 = $this->J2;
		// Dessin au format SVG.
		$couleurV = $this->colorV;
		$couleurY = $this->colorY;
		$couleurGauche = $this->colorLeft;
		$couleurDroite = $this->colorRight;
		$svg = '<?xml version="1.0" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
		$svg .= '<svg width="'.$this->xmax.'" height="'.$this->ymax.'" version="1.1" xmlns="http://www.w3.org/2000/svg">';
		$svg .= '<title>logo'.($this->name != "" ? '-'.$this->name : '').'</title>';
		// Intérieur gauche.
		$svg .= '<polygon points="'.$E.' '.$F.' '.$K.'" style="fill:'.$couleurGauche.'; stroke:'.$couleurGauche.'; stroke-width:0.5; stroke-linejoin:miter; stroke-miterlimit:5;"/>';
		// Intérieur droit.
		$svg .= '<polygon points="'.$G.' '.$H.' '.$I.'" style="fill:'.$couleurDroite.'; stroke:'.$couleurDroite.'; stroke-width:0.5; stroke-linejoin:miter; stroke-miterlimit:5;"/>';
		// Y.
		$svg .= '<polygon points="'.$A.' '.$B.' '.$C.' '.$H.' '.$G.' '.$J1.' '.$J2.' '.$F.' '.$E.'" style="fill:'.$couleurY.'; stroke:black; stroke-width:0.5; stroke-linejoin:miter; stroke-miterlimit:5;"/>';
		// V.
		$svg .= '<polygon points="'.$A.' '.$E.' '.$J.' '.$H.' '.$C.' '.$D.'" style="fill:'.$couleurV.'; stroke:black; stroke-width:0.5; stroke-linejoin:miter; stroke-miterlimit:5;"/>';
		$svg .= '</svg>';
		return $svg;
	}
	public function matrixImage() {
		// Calcul des coordonnées des points.
		$this->computePoints();
		// Récupération des points.
		$A = $this->A;
		$B = $this->B;
		$C = $this->C;
		$D = $this->D;
		$H = $this->H;
		$E = $this->E;
		$G = $this->G;
		$F = $this->F;
		$I = $this->I;
		$J = $this->J;
		$K = $this->K;
		$J1 = $this->J1;
		$J2 = $this->J2;
		// Récupération des couleurs.
		$couleurV = $this->colorV;
		$couleurY = $this->colorY;
		$couleurGauche = $this->colorLeft;
		$couleurDroite = $this->colorRight;
		// Dessin de l'image matricielle.
		$image = imagecreatetruecolor($this->xmax + 1, $this->ymax + 1);
		if($image) {
			$noir = imagecolorallocate($image, 0, 0, 0);
			imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
			imagefilledpolygon($image, array($E->x, $E->y, $F->x, $F->y, $K->x, $K->y), 3, imagecolorallocate($image, $couleurGauche->r, $couleurGauche->g, $couleurGauche->b));
			imagepolygon($image, array($E->x, $E->y, $F->x, $F->y, $K->x, $K->y), 3, $noir);
			imagefilledpolygon($image, array($G->x, $G->y, $H->x, $H->y, $I->x, $I->y), 3, imagecolorallocate($image, $couleurDroite->r, $couleurDroite->g, $couleurDroite->b));
			imagepolygon($image, array($G->x, $G->y, $H->x, $H->y, $I->x, $I->y), 3, $noir);
			imagefilledpolygon($image, array($A->x, $A->y, $B->x, $B->y, $C->x, $C->y, $H->x, $H->y, $G->x, $G->y, $J1->x, $J1->y, $J2->x, $J2->y, $F->x, $F->y, $E->x, $E->y), 9, imagecolorallocate($image, $couleurY->r, $couleurY->g, $couleurY->b));
			imagepolygon($image, array($A->x, $A->y, $B->x, $B->y, $C->x, $C->y, $H->x, $H->y, $G->x, $G->y, $J1->x, $J1->y, $J2->x, $J2->y, $F->x, $F->y, $E->x, $E->y), 9, $noir);
			imagefilledpolygon($image, array($A->x, $A->y, $E->x, $E->y, $J->x, $J->y, $H->x, $H->y, $C->x, $C->y, $D->x, $D->y), 6, imagecolorallocate($image, $couleurV->r, $couleurV->g, $couleurV->b));
			imagepolygon($image, array($A->x, $A->y, $E->x, $E->y, $J->x, $J->y, $H->x, $H->y, $C->x, $C->y, $D->x, $D->y), 6, $noir);
			return $image;
		};
		return false;
	}
	public function drawSvg() {
		header("Content-type: image/svg+xml");
		echo $this->svg();
	}
	public function drawJpeg() {
		header("Content-type: image/jpeg");
		imagejpeg($this->matrixImage());
	}
	public function drawPng() {
		header("Content-type: image/png");
		imagepng($this->matrixImage());
	}
}
?>