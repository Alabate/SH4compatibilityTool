<?php
session_start();
$version = '312E3031'; //1.01
ini_set("display_errors",0);
if(!empty($_FILES['file']['name']))
{	
	// echo $_FILES['file']['name'].'<br/>';
	// require_once('functions.php');
		
		//Get the g1a
			$InFile = file_get_contents($_FILES['file']['tmp_name']);
			$InFileSize = strlen($InFile);
			$InFile = bin2hex($InFile);
		//Check if the file is a g1a
			if(strtolower($InFile[16].$InFile[17]) != '0c')
			{
				$_SESSION['msg_text']='The file is not recognized as a .g1a';
				$_SESSION['msg_color']='red';
				header("Location: ../");
				exit;
			}
		//Check if the is not already edited by this tool
			if(strtolower(substr($InFile,-60,52)) == '000053483420436f6d7061746962696c69747920546f6f6c2076')
			{
				$_SESSION['msg_text']='This file has already been modified by this tool. (version '.hex2bin(substr($InFile,-8)).')';
				$_SESSION['msg_color']='red';
				header("Location: ../");
				exit;
			}
		//Verify if the file length is a divisible by 4 (cause the code that i add to the end is done only for this)
			if($InFileSize%4 != 0)
			{
				$OutFile = str_pad($InFile, ($InFileSize+4-($InFileSize%4))*2, '0', STR_PAD_RIGHT);
				$InFileSize = $InFileSize+4-($InFileSize%4);
			}
			else
				$OutFile = $InFile;
		//Input functions fix
			$inputFix = false;
			$GetKeyStateAdress = str_pad(dechex($InFileSize+0x300000), 8, '0', STR_PAD_LEFT);
			//IsKeyDown
			$isKeyDownCode = '4f227ffc63f3bee52f367f0463f0633c43118b0384f1600c401189037f044f26000be000bf5664f37f044f26000b0009';
			$isKeyDownPosition = strpos($OutFile,$isKeyDownCode);
			if($isKeyDownPosition !== false)
			{
				//Test if there is more than one function
				if(strpos($OutFile,$isKeyDownCode,$isKeyDownPosition+1) !== false)
				{
					$_SESSION['msg_text']='Error : we found more than one IsKeyDown function !';
					$_SESSION['msg_color']='red';
					header("Location: ../");
					exit;
				}
				//Replacement
				$inputFix = true;
				if(!isset($_POST['slow']))$slowcode = '00'; //fastmode
				else $slowcode = '01'; //IsKeyDown Slowmode
				$OutFile = substr($OutFile,0,$isKeyDownPosition+(0xc*2)) . '64F3E5'.$slowcode.'D003400B000900097F044F26000B0009' . $GetKeyStateAdress . substr($OutFile,$isKeyDownPosition+(0xc+20+4)*2);				
			}
			// echo (strlen($OutFile)/2).'->0 <br/> ';
			//IsKeyUp
			$isKeyUpCode = '4f227ffc63f3becd2f367f0463f0633c43118b0384f1600c401189037f044f26000be000d208420b64f3200800297f044f26000b00095555a40001000000aaaa0000ff00a4000120';
			$isKeyUpPosition = strpos($OutFile,$isKeyUpCode);
			if($isKeyUpPosition !== false)
			{
				//Test if there is more than one function
				if(strpos($OutFile,$isKeyUpCode,$isKeyUpPosition+1) !== false)
				{
					$_SESSION['msg_text']='Error : we found more than one IsKeyUp function !';
					$_SESSION['msg_color']='red';
					header("Location: ../");
					exit;
				}
				//Replacement
				$inputFix = true;
				if(!isset($_POST['slow']))$slowcode = '00'; //fastmode
				else $slowcode = '02'; //IsKeyUp Slowmode
				$OutFile = substr($OutFile,0,$isKeyUpPosition+(0xc*2)) . '64F3E5'.$slowcode.'D004400B000900096007C9017F044F26000B0009' . $GetKeyStateAdress . substr($OutFile,$isKeyUpPosition+(0xc+24+4)*2);				
			}
			// echo (strlen($OutFile)/2).'->1 <br/> ';
		//KeyDown : pc%4=0
			$keydownfix=false;
			$KeyDownCode = '2fe6634c2fd6e50f2fc643092fb625492fa643092f96665c4f2260637ffc40112f308b01a005c90760077001c907600770016403';
			$KeyDownPosition = strpos($OutFile,$KeyDownCode);
			if($KeyDownPosition !== false)
			{
				//Test if there is more than one function
				if(strpos($OutFile,$KeyDownCode,$KeyDownPosition+1) !== false)
				{
					$_SESSION['msg_text']='Error 1 : we found more than one KeyDown function !';
					$_SESSION['msg_color']='red';
					header("Location: ../");
					exit;
				}
				$inputFix = true;
				$keydownfix=true;
			}
		//KeyDown : pc%4=2
			$KeyDown2Code = '634c2fe6e50f2fd643092fc625492fb643092fa6665c2f9660634f2240117ffc2f308b01a005c90760077001c90760077001';
			$KeyDown2Position = strpos($OutFile,$KeyDown2Code);
			if($KeyDown2Position !== false)
			{
				//Test if there is more than one function
				if(strpos($OutFile,$KeyDown2Code,$KeyDown2Position+1) !== false || $keydownfix)
				{
					$_SESSION['msg_text']='Error 2 : we found more than one KeyDown function !';
					$_SESSION['msg_color']='red';
					header("Location: ../");
					exit;
				}
				$inputFix = true;
				$keydownfix=true;
				//Decallage pour avoir pc%4=0
				$OutFile = substr($OutFile,0,$KeyDown2Position) . '0009'. substr($OutFile,$KeyDown2Position+4);
				$KeyDownPosition = $KeyDown2Position+4;
			}
		//KeyDown replacement
			if($keydownfix)
			{
				if(!isset($_POST['slow']))$slowcode = '00'; //fastmode
				else $slowcode = '03'; //IsKeyDown Slowmode
				$OutFile = substr($OutFile,0,$KeyDownPosition) . '4F222F162F56E0FC6143410D6043C90F4118310C41282F1664F3E5'.$slowcode.'D004400B00097F0465F661F64F26000B00090009' . $GetKeyStateAdress . substr($OutFile,$KeyDownPosition+54+2+40+8);
			}
		//Part to add at the end for IsKeyDown, IsKeyUp, and KeyDown
			if($inputFix)
			{
				$delayLoop = 'F6'; //-10
				$OutFile .='4F222F162F262F362F662F762F862F96684369537FFC64F37FFC65F37FFC66F37FFC67F3D21F420BE0157F0866f07F0460f07F044018360C678084816803E0003087897BE1093817897830778976E106371789739023360389596083C907300CE603460D6083C907E501450D6557D00E401EE00830878B1960639111201AC101C50C910E2019CBAAC10CB065E4'.$delayLoop.'6053C022C438C9F0CB0FC038A01700090202AAAAFF0080010070A4000100902CC101C50C912A2019CBAA206AC10CB04CE4'.$delayLoop.'E0FFC022C438C9F0205BC038B044E4'.$delayLoop.'C4206607607B460DE0012609B03CE4'.$delayLoop.'9012C101C50C91102019CBAAC10CB033E4'.$delayLoop.'900BC101C50C91072019CB55C10CB02AE4'.$delayLoop.'A01B0009AAAAFF005555E00039078B007903D1086083C801318C890071FE6010E101417D201800296007C9016603A00400090000A44B0001E600B00B6493606369F668F667F666F663F662F661F64F26000B0009E00034038B14344CC702344C304C6102A00F0009'.
				// Number of loop to emulate old functions
				'00000001'. //fastest (can't be under 1)
				'000006F7'. //IsKeyDown SH3
				'00012D1F'. //IsKeyUp SH3
				'00000001'. //KeyDown SH3
				'000005CD'. //IsKeyDown SH4
				'0000F1A8'. //IsKeyUp SH4
				'00000001'. //KeyDown SH4
				'614B41108BFD000B00090009';
			}			
		//Syscall bug fix
			$position = 0;
			$SyscallFix = false;
			$syscallCodeAddress = str_pad(dechex((strlen($OutFile)/2)+0x300000), 8, '0', STR_PAD_LEFT);
			while(isset($OutFile[$position+3]))
			{
				if($OutFile[$position]=='4' && strtolower($OutFile[$position+1])=='f' && $OutFile[$position+2]=='2' &&$OutFile[$position+3]=='2')
				{
					//The instruction of the actual line is 4F22: sts.l pr,@-15
					//The $position+4 or $position+8 instruction is d3xx:mov.l @(h'xx,pc),r3 // and @(h'xx,pc) contain the an address >= 0x08100000 (and we will suppose < 0x08200000)
					$pc=0;
					$valide = true;
					if(strtolower($OutFile[$position+4]=='d') && $OutFile[$position+5]=='3')
					{
						$disp = ord(hex2bin($OutFile[$position+6].$OutFile[$position+7]));
						$pc = ($position/2)+4;
						$subOffset = 4;
					}
					elseif(strtolower($OutFile[$position+8]=='d') && $OutFile[$position+9]=='3')
					{
						$disp = ord(hex2bin($OutFile[$position+10].$OutFile[$position+11]));
						$pc = ($position/2)+4+4;
						$subOffset = 8;
					}
					else
						$valide=false;
					//Check if the address >= 0x08100000 and <0x08200000
					if($valide)
					{
						$ToChange_Address = (($pc&0xFFFFFFFC)+($disp<<2))*2;
						if(substr($OutFile,$ToChange_Address,3) != '081')
							$valide=false;
					}
					//Search the row 92XX: mov.w @(h'xx,pc),r2 // This is the instruction that get the syscall number, so if I want it, I can. (just do as above)
					if($valide)
					{
						$valide=false;
						for($i=0;$i<=4;$i++)
						{
							if($OutFile[$position+$subOffset+($i*4)] == 'd' && $OutFile[$position+$subOffset+($i*4)+1] == '3')
							{
								$subOffset+=$i*4;
								$valide = true;
								break;
							}
						}
					}
					//Search the row 6032: mov @R3,r0 // I need to edit this line to : mov r3,r0
					if($valide)
					{
						$valide=false;
						for($i=0;$i<=4;$i++)
						{
							if(substr($OutFile,$position+$subOffset+($i*4),4) == '6032')
							{
								$ToChange_Instruction = $position+$subOffset+($i*4);
								$subOffset+=$i*4;
								$valide = true;
								break;
							}
						}
					}
					//Search the row 400b: jsr @r0 //jump instruction
					if($valide)
					{
						$valide=false;
						for($i=0;$i<=4;$i++)
						{
							if(substr($OutFile,$position+$subOffset+($i*4),4) == '400b')
							{
								$subOffset+=$i*4;
								$valide = true;
								break;
							}
						}
					}
					//Fixing
					if($valide)
					{
						$SyscallFix = true;
						//Change the mov instruction
						$OutFile = substr($OutFile,0,$ToChange_Instruction) . '6033' . substr($OutFile,$ToChange_Instruction+4);
						//Change the value moved (to the address of the code, to let him jump to it)
						$OutFile = substr($OutFile,0,$ToChange_Address) . $syscallCodeAddress . substr($OutFile,$ToChange_Address+8);					
					}
				}
				$position+=4;
			}
		//Add the code for syscall if needed
			if($SyscallFix)
			{
				$OutFile .= 'D201422B60F2000080010070';
			}
		//Add the version mark
			$OutFile .= '0000000053483420436F6D7061746962696C69747920546F6F6C2076'.$version; //SH4 Compatibility Tool v$version
		//Recalcul header
			$OutFileSize = strlen($OutFile)/2;
			if($InFileSize != $OutFileSize)
			{
				//OFFSET 0xE : last filesize byte + 65(0x41) and NOT (inverted)
					$offset0xE = $OutFileSize + 0x41;
					$offset0xE = 0xFFFFFFFF - $offset0xE; 
					$offset0xE = dechex($offset0xE);
					$offset0xE = str_pad($offset0xE, 2, '0', STR_PAD_LEFT);
					$offset0xE = substr($offset0xE, -2); // take the last byte
				//OFFSET 0x10 : filesize NOT (inverted)
					$offset0x10 = 0xFFFFFFFF - $OutFileSize; 
					$offset0x10 = dechex($offset0x10);// to hex
					$offset0x10 = str_pad($offset0x10, 8, '0', STR_PAD_LEFT);
				//OFFSET 0x14 : last filesize byte + 184(0xB8) and NOT (inverted)
					$offset0x14 = $OutFileSize + 0xB8;
					$offset0x14 = 0xFFFFFFFF - $offset0x14; 
					$offset0x14 = dechex($offset0x14);
					$offset0x14 = str_pad($offset0x14, 2, '0', STR_PAD_LEFT);
					$offset0x14 = substr($offset0x14, -2);
				//OFFSET 0x1f0 : filesize
					$offset0x1f0 = $OutFileSize; 
					$offset0x1f0 = dechex($offset0x1f0);// to hex
					$offset0x1f0 = str_pad($offset0x1f0, 8, '0', STR_PAD_LEFT);
				//Modifs
					$OutFile = substr($OutFile,0,0xE*2). $offset0xE . substr($OutFile,0xf*2,2).
						$offset0x10 . substr($OutFile,0x14*2,0x1dc*2) . $offset0x1f0 . substr($OutFile,0x1f4*2);
						// echo $InFileSize . ' - ' .$OutFileSize;exit;
			}
		//making
			$fileContent = pack('H*',$OutFile);
		//sending
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment;filename="'.substr($_FILES['file']['name'],0,-4).'.g1a"');
			header('Cache-Control: max-age=0');
			$fh = fopen('php://output', 'wb');
			fwrite($fh, $fileContent);
			fclose($fh);
			
}
else
{
	header("Location: ../");
}
?>