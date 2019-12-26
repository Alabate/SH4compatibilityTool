# Problem solved by this tool

## sh3/sh4 compatible input function: `_GetKeyState`
Most of the problem solved by this tool are keyboard input related. So the first step was to create an sh3/sh4 compatible function to get keyboard key state. This function is injected at the end of the program and because of that we also need to rewrite the header, but that's easy thanks to Simon Lothar and his documentation.

You can find the full commented assembly source of the function into [_getKeyState.asm](./_getKeyState.asm).

This function is faster than most input functions (except KeyDown). So using the following code, I tested the speed of each functions first:

```c++
int AddIn_main(int isAppli, unsigned short OptionNum)
{
   unsigned int timeBegin;
   unsigned int duration;
   char string[9];
   int i;
   while(1)
   {
      timeBegin = RTC_GetTicks();//RTC_GetTicks is a syscall documented in FxReverse
      for(i=0;i<5000;i++)
      {
         key_down(K_EXE);//Change this function here
      }
      duration = RTC_GetTicks()-timeBegin;
      intToHex(duration, string);
      
      Bdisp_AllClr_DDVRAM();
      locate(1,1);
      Print((unsigned char*)string);
      Bdisp_PutDisp_DD();
   }

    return 1;
}

void intToHex(unsigned int in, char* string)
{
   string[0] = nibbleToHex((unsigned char)in>>28);
   string[1] = nibbleToHex((unsigned char)(in>>24)&0xF);
   string[2] = nibbleToHex((unsigned char)(in>>20)&0xF);
   string[3] = nibbleToHex((unsigned char)(in>>16)&0xF);
   string[4] = nibbleToHex((unsigned char)(in>>12)&0xF);
   string[5] = nibbleToHex((unsigned char)(in>>8)&0xF);
   string[6] = nibbleToHex((unsigned char)(in>>4)&0xF);
   string[7] = nibbleToHex((unsigned char)in&0xF);
   string[8] = 0;
}


char nibbleToHex(unsigned char in)
{
   char out;
   if(in <= 9)
      out = 0x30 + (unsigned int)in;
   else
   {
      switch(in-10)
      {
         case  0 : out = 0x61; break;
         case  1 : out = 0x62; break;
         case  2 : out = 0x63; break;
         case  3 : out = 0x64; break;
         case  4 : out = 0x65; break;
         case  5 : out = 0x66; break;
      }
   }
   return out;
}
```

And here are the result:

It shows the number of ticks taken to execute the function.

|                                      | Original | _GetKeyState SH3 | _GetKeyState SH4 |
|--------------------------------------|----------|------------------|------------------|
| IsKeyDown                            | 0xb2     | 0x17(miss 155)   | 0x11(miss 161)   |
| IsKeyDown with 0x1000 waitloop       | ---      | 0x17b            | 0x1cd            |
| IsKeyDown ; sh3:0x12D1F ; sh4:0xF1A8 | ---      | 0xb2             | 0xb2             |
| IsKeyUp                              | 0x1a43   | 0x17(miss 6700)  | 0x11(miss 6706)  |
| IsKeyUp ; sh3:0x12D1F ; sh4:0xF1A8   | ---      | 0x1a42           | 0x1a4a           |
| KeyDown                              | 0x9      | 0x11             | 0xd              |

 * 0x1000 loop takes 0x164 ticks to be executed on SH3 => 1024/89 loop/ticks
 * 0x1000 loop takes 0x1BC ticks to be executed on SH4 => 1024/111 loop/ticks
 * KeyDown is faster than `_GetKeyState`


## IsKeyDown function from FxLib
The IsKeyDown Function read directly the keyboard input state without using a syscall. But with power graphic 2 the keyboard connections to the CPU changed and it's not possible anymore to read the keyboard the same way.

As Casimo showed us with his solution in C, Power Graphic 2 calculators have a specific keyboard register that we can read easily. The solution used by this tool is it to lookup for the binary code of the IsKeyDown function, and replacing it with a new one that jump to `_GetKeyState`.

Firstly, we look for the `isKeyDown` function:

```
4f227ffc63f3bee52f367f0463f0633c43118b0384f1600c401189037f044f26000be000bf5664f37f044f26000b0009
```

Then we replace its content starting at offset `0xc` of the function. (The first bytes replaced are `0x63f0`).

```
_IsKeyDownReplacement
    ; before this injection, there is call of _KeyCodeConvert that put an array of two byte that respectivly contain the col and the line in the stack
    ; So we put them into registers to use them letter in the _GetKeyState function that we will call
    mov r15,r4 ; first param of the _GetKeyState function
    mov #1,r5 ; set slowmode of the _GetKeyState function
    ; Jump to _GetKeyState
    mov.l GetKeyStateAddress,r0
    jsr @r0 ;call _GetKeyState
    nop
    nop
    ; after _GetKeyState
    ; return to the calling position
    add #4,r15
    lds.l @r15+,pr
    rts
    nop
GetKeyStateAddress:
    .data.l h'xxxxxxxx ; addres of the compatible _GetKeyState function
```

## IsKeyUp function from FxLib
This function doesn't works at all like IsKeyDown. This one use the syscall `0x24C` called "Chattering" by the fxLib.

The prototype seem to be :

```C++
int Chattering(unsigned char* coord);
```

with key coordinates in an array of two cells, the first is the cols, the seconds is the row of the key. This syscall return 1 if the key is pressed.


Syscall are writed in the OS code, so when there is an OS update, syscall are generally updated to works on the new hardware (it's the case of a lot of usefull syscall). But this one seem to not work on SH4 calc, it allays return 0. So maybe Casio choosed to disable this syscall.

Anyway, no matter the original implementation, we will also use `_GetKeyState` and just put a "not" at the end.

```asm
_IsKeyUpReplacement
    ; before this injection, there is call of _KeyCodeConvert that put an array of two byte that respectivly contain the col and the line in the stack
    ; So we put them into registers to use them letter in the _GetKeyState function that we will call
    mov r15,r4 ; first param of the _GetKeyState function
    mov #2,r5 ; set slowmode of the _GetKeyState function
    ; Jump to _GetKeyState
    mov.l GetKeyStateAddress2,r0
    jsr @r0 ;call _GetKeyState
    nop
    nop
    ; after _GetKeyState
    ; Negate _GetKeyState output
    not r0,r0
    and #1,r0
    ; return to the calling position
    add #4,r15
    lds.l @r15+,pr
    rts
    nop
GetKeyStateAddress2:
    .data.l h'xxxxxxxx ; addres of the compatible _GetKeyState function
```

## KeyDown function

This is the same problem as the IsKeyDown function, the I/O register change so we can not read the keyboard input on the SH4 cpu. To identify this function, this was a little harder, because this function is not precompilated like the FxLib. So I found 2 binary implementation of this function in asm code:

* when the first line offset is equal to 0 modulo 4
* when it's equal to 2 modulo 4.

Some asm code work only when it's on a mod4=0, like 

```
mov.l @(h'4,pc),r0
```

be cause it can read a longword (4byte) only at an offset mod4=0 and the number in parameter need to be divisible by 4. That explains the difference between the two implementations.

So we replace the KeyDown function with this code

```asm
_KeyDownReplacement ; put this at beginning of the KeyDown function. (the first four byte replaced : 2FE6634C)
   ;before : keycode in r4 ; keycode=col<<4 + row
   sts.l pr,@-r15
   mov.l r1,@-r15
   mov.l r5,@-r15
   ; add #-2,r15 ;r15 need to always contain a number divisible by 4 (because when we put a longword of 4byte in the stack, we can only put it on adress multiple of 4)
   ;get the col
   mov #-4,r0
   mov r4,r1
   shld r0,r1
   ;get the row
   mov r4,r0
   and #h'f,r0
   ; mov.b r0,@-r15
   ; mov.b r1,@-r15
   ;prepartion of the array content
   shll8 r1
   add r0,r1
   shll16 r1
   mov.l r1,@-r15
   ;prepare _GetKeyState call
   mov r15,r4 ; get array address
   mov #3,r5 ; set slowmode of _GetKeyState function
   mov.l GetKeyStateAddress3,r0
   jsr @r0 ;call _GetKeyState
   nop
   ;after _GetKeyState
   add #4,r15
   mov.l @r15+,r5
   mov.l @r15+,r1
   lds.l @r15+,pr
   rts
   nop
GetKeyStateAddress3:
    .data.l h'xxxxxxxx ; addres of the compatible _GetKeyState function
```
## monochromeLib and syscall call method
Monochromelib call OS syscall with this C++ code

```c++
static int SysCallCode[] = {0xD201422B,0x60F20000,0x80010070};
static int (*SysCall)( int R4, int R5, int R6, int R7, int FNo ) = (void*)&SysCallCode;
char* ML_vram_adress()
{
    return (char*)((*SysCall)(0, 0, 0, 0, 309));
}
```

The array SysCallCode is writed in the memory at the address > `0x0810000`. Once this array is writen into this memory, it jump on it. The content of this array is a binary code to run syscall. That's mean that this array need to be stored in a memory that can be read, writen and executed. This was possible in SH3, but on the SH4 CPU, this memory cannot be executed.

An easy solution is to avoid writting this array on this memory by making it a "const". As const, it will stay in the programm "instruction list", and will not be copied anywhere. And the programm instruction list is obviously still readable and executable.

```C++
static const int SysCallCode[] = {0xD201422B,0x60F20000,0x80010070};
static int (*SysCall)( int R4, int R5, int R6, int R7, int FNo ) = (void*)&SysCallCode;
char* ML_vram_adress()
{
    return (char*)((*SysCall)(0, 0, 0, 0, 309));
}
```

The code above is not a solution for us cause we cannot edit the C without sources.
Another problem is that there is many binary implementation for this solution, because it depends of the parameters. And it's too small to look it up like we do on `isKeyDown`.

Here is an assembly that can be generated from the C++ code

```asm
mov.l @(H'114,pc),r3 ; It get the address where the address of the array is written, here it's 0x08100014
mov.l @(H'10c,pc),r2 ; It get the syscall number from the parameter, here it's 0x135
mov.l @r3,r0 ; It get the address of the array, here it's 0x08100008
jsr @r0 ; Jump to the array
mov.l r2,@-r15 ; This is executed just before to jump : it put the syscall number in the stack
```

So to solve the problem, I put this code at the end of the file

```asm
_SyscallFix
   mov.l #h'80010070,r2 ; the syscall table (where we jump to execute a syscall)
   jmp @r2 ; Jump to the syscall table
   mov.l @r15,r0 ; Just before to jump, put the value in the stack to the register r0 (the value in the stack is the syscall number)
```

And with our original code, i edit it a little : 

```asm
mov.l @(H'114,pc),r3 ; I change the value pointed to be the address of my function SyscallFix (added at the end of the file)
mov.l @(H'10c,pc),r2
mov.l r3,r0 ; change to put the address get at the first line in r0
jsr @r0 ; Jump to the my added code
mov.l r2,@-r15
```
And it work well with this solution.

The hardest part is how we find the original implementation, because it changes everytime. All the line that are here everytime, but sometime separated by other instructions.
