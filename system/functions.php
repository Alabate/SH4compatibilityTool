<?php
$Fxi2RawArray = array(
	255 => "18",		254 => "19",		253 => "1a",		252 => "1b",		251 => "1c",
	250 => "1d",		249 => "1e",		248 => "1f",		247 => "10",		246 => "11",
	245 => "12",		244 => "13",		243 => "14",		242 => "15",		241 => "16",
	240 => "17",		239 => "08",		238 => "09",		237 => "0a",		236 => "0b",
	235 => "0c",		234 => "0d",		233 => "0e",		232 => "0f",		231 => "00",
	230 => "01",		229 => "02",		228 => "03",		227 => "04",		226 => "05",
	225 => "06",		224 => "07",		223 => "38",		222 => "39",		221 => "3a",
	220 => "3b",		219 => "3c",		218 => "3d",		217 => "3e",		216 => "3f",
	215 => "30",		214 => "31",		213 => "32",		212 => "33",		211 => "34",
	210 => "35",		209 => "36",		208 => "37",		207 => "28",		206 => "29",
	205 => "2a",		204 => "2b",		203 => "2c",		202 => "2d",		201 => "2e",
	200 => "2f",		199 => "20",		198 => "21",		197 => "22",		196 => "23",
	195 => "24",		194 => "25",		193 => "26",		192 => "27",		191 => "58",
	190 => "59",		189 => "5a",		188 => "5b",		187 => "5c",		186 => "5d",
	185 => "5e",		184 => "5f",		183 => "50",		182 => "51",		181 => "52",
	180 => "53",		179 => "54",		178 => "55",		177 => "56",		176 => "57",
	175 => "48",		174 => "49",		173 => "4a",		172 => "4b",		171 => "4c",
	170 => "4d",		169 => "4e",		168 => "4f",		167 => "40",		166 => "41",
	165 => "42",		164 => "43",		163 => "44",		162 => "45",		161 => "46",
	160 => "47",		159 => "78",		158 => "79",		157 => "7a",		156 => "7b",
	155 => "7c",		154 => "7d",		153 => "7e",		152 => "7f",		151 => "70",
	150 => "71",		149 => "72",		148 => "73",		147 => "74",		146 => "75",
	145 => "76",		144 => "77",		143 => "68",		142 => "69",		141 => "6a",
	140 => "6b",		139 => "6c",		138 => "6d",		137 => "6e",		136 => "6f",
	135 => "60",		134 => "61",		133 => "62",		132 => "63",		131 => "64",
	130 => "65",		129 => "66",		128 => "67",		127 => "98",		126 => "99",
	125 => "9a",		124 => "9b",		123 => "9c",		122 => "9d",		121 => "9e",
	120 => "9f",		119 => "90",		118 => "91",		117 => "92",		116 => "93",
	115 => "94",		114 => "95",		113 => "96",		112 => "97",		111 => "88",
	110 => "89",		109 => "8a",		108 => "8b",		107 => "8c",		106 => "8d",
	105 => "8e",		104 => "8f",		103 => "80",		102 => "81",		101 => "82",
	100 => "83",		99 => "84",			98 => "85",			97 => "86",			96 => "87",
	95 => "b8",			94 => "b9",			93 => "ba",			92 => "bb",			91 => "bc",
	90 => "bd",			89 => "be",			88 => "bf",			87 => "b0",			86 => "b1",
	85 => "b2",			84 => "b3",			83 => "b4",			82 => "b5",			81 => "b6",
	80 => "b7",			79 => "a8",			78 => "a9",			77 => "aa",			76 => "ab",
	75 => "ac",			74 => "ad",			73 => "ae",			72 => "af",			71 => "a0",
	70 => "a1",			69 => "a2",			68 => "a3",			67 => "a4",			66 => "a5",
	65 => "a6",			64 => "a7",			63 => "d8",			62 => "d9",			61 => "da",
	60 => "db",			59 => "dc",			58 => "dd",			57 => "de",			56 => "df",
	55 => "d0",			54 => "d1",			53 => "d2",			52 => "d3",			51 => "d4",
	50 => "d5",			49 => "d6",			48 => "d7",			47 => "c8",			46 => "c9",
	45 => "ca",			44 => "cb",			43 => "cc",			42 => "cd",			41 => "ce",
	40 => "cf",			39 => "c0",			38 => "c1",			37 => "c2",			36 => "c3",
	35 => "c4",			34 => "c5",			33 => "c6",			32 => "c7",			31 => "f8",
	30 => "f9",			29 => "fa",			28 => "fb",			27 => "fc",			26 => "fd",
	25 => "fe",			24 => "ff",			23 => "f0",			22 => "f1",			21 => "f2",
	20 => "f3",			19 => "f4",			18 => "f5",			17 => "f6",			16 => "f7",
	15 => "e8",			14 => "e9",			13 => "ea",			12 => "eb",			11 => "ec",
	10 => "ed",			9 => "ee",			8 => "ef",			7 => "e0",			6 => "e1",
	5 => "e2",			4 => "e3",			3 => "e4",			2 => "e5",			1 => "e6",
	0 => "e7",		
	);
	
	
	function fxiDecode($text)
	{
		global $Fxi2RawArray;
		$fileSize = strlen($text);
		$Return="";
		for($i = 0 ; $i < $fileSize; $i++)
		{
			if(isset($Fxi2RawArray[ord($text[$i])]))
			{
				$Return.= $Fxi2RawArray[ord($text[$i])];
			}
			else
			{
				echo "Erreur : caractere non reconnu (erreur impossible)";exit;
			}
		}
		return $Return;
	}
	
	function getFxiPic($text) //transform fxi pic an array of 4 array reprent all 4 colors. array of colors are coded like a g1m sheet (natural organisation : one bit represente one pixel) ; color order : Blue, orange, green, white
	{	
		$textSize = strlen($text);
		$actuelColor = 0;
		$x = 15;
		$y = 63;
		for($i = 8 ; $i < $textSize; $i+=2)
		{
			//changement de couleur
				if($i >=($actuelColor*2056+2056) )
				{
					$actuelColor++;
					$i+=8;
					$x = 15;
					$y = 63;
				}
			//changement de colonne
				if($y<0)
				{
					$y=63;
					$x--;
				}
			//enregistrement de la case
				$Return[$actuelColor][$y*16+$x] = hexdec($text[$i].$text[$i+1]);
				$y--;
		}
		
		return $Return;
	}
	
	function getG1mPic($text) //transform G1m pic an array of 4 array reprent all 2 colors. array of colors are coded like a g1m sheet (natural organisation : one bit represente one pixel) ; color order : Blue, orange, green, white
	{
		$textSize = strlen($text);
		$actuelColor = 0;
		$i = 0;
		while($actuelColor < 2 && $actuelColor*2048+$i<$textSize)
		{
			if($i >= 2048 )
			{
				$i = 0;
				$actuelColor++;
			}			
			$Return[$actuelColor][$i/2] = hexdec($text[$actuelColor*2048+$i].$text[$actuelColor*2048+$i+1]);
			$i+=2;
		}
		// print_r($Return);exit;
		return $Return;
	}
	//color=array(array(255,0,0),array(0, 0, 255),array(0, 128, 0),array(66, 174, 9)) 
	
	
	function imageResizeAlpha($src, $coef)
	{
		$w = imagesx($src)*$coef;
		$h = imagesy($src)*$coef;
        $temp = imagecreatetruecolor($w, $h);
		imagealphablending($temp, false);
		imagesavealpha($temp, true);
		$trans_layer_overlay = imagecolorallocatealpha($temp, 0, 0, 0, 127);
		imagefill($temp, 0, 0, $trans_layer_overlay);
        imagecopyresized($temp, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
        return $temp;
	}
	
	//=array(array(255,0,0),array(0, 0, 255),array(0, 128, 0),array(66, 174, 9))
		// $actualPic = 1;
		//0 = rouge
		//1 = bleu
		//2 = vert
		//3 = white
	function writePics($pics, $colors, $size=3)
	{
		$nbrPics = count($pics);
		header("Content-type: image/png");
		$image = imagecreate(128,64); 
		$fond = imagecolorallocate($image, 255,255,255);
		imagecolortransparent($image,$fond);
		for($actualPic = 0 ; $actualPic < $nbrPics; $actualPic++)
		{
			// echo $actualPic;
			$AColor = imagecolorallocate($image, $colors[$actualPic][0], $colors[$actualPic][1], $colors[$actualPic][2]);
			for($xy = 0 ; $xy < 1024; $xy++)
			{
				$y = (int)($xy/16);
				$x = ($xy%16)*8;
				if($pics[$actualPic][$xy]&1)//00000001
					ImageSetPixel($image, $x+7, $y, $AColor);
				if(($pics[$actualPic][$xy]&2)>>1)//00000010
					ImageSetPixel($image, $x+6, $y, $AColor);
				if(($pics[$actualPic][$xy]&4)>>2)//00000100
					ImageSetPixel($image, $x+5, $y, $AColor);
				if(($pics[$actualPic][$xy]&8)>>3)//00001000
					ImageSetPixel($image, $x+4, $y, $AColor);
				if(($pics[$actualPic][$xy]&16)>>4)//00010000
					ImageSetPixel($image, $x+3, $y, $AColor);
				if(($pics[$actualPic][$xy]&32)>>5)//00100000
					ImageSetPixel($image, $x+2, $y, $AColor);
				if(($pics[$actualPic][$xy]&64)>>6)//01000000
					ImageSetPixel($image, $x+1, $y, $AColor);
				if(($pics[$actualPic][$xy]&128)>>7)//10000000
					ImageSetPixel($image, $x, $y, $AColor);
			}
		}
		//resizing
			if($size >=2)
			{
				$image = imageResizeAlpha($image,$size);
			}
		//senging
			imagepng($image);
	}
?>