;param 1 (in r4) : Adress of an array of two unsigned char, with in the first cell the col, and in the second the row
;param 2 (in r5) : slowMode : this determine the time this function will wait to emulate the olds functions
;      -n : number of loop
;      0 : fatest as possible
;      1 : = duration of IsKeyDown function
;      2 : = duration of IsKeyUp function
;      3 : = duration of KeyDown function
;return (in r0) 1 if the key is pressed.
_GetKeyState
    ;First put current register values in the stack
    sts.l   pr,@-r15
    mov.l   r1,@-r15
    mov.l   r2,@-r15
    mov.l   r3,@-r15
    mov.l   r6,@-r15
    mov.l   r7,@-r15
    mov.l   r8,@-r15
    mov.l   r9,@-r15
    mov r4,r8 ; first param
    mov r5,r9 ; second param
    
;check the os version with the syscall 0x0015 | if I use only 1 byte for chars, and 2 for short, it crash on all calc but not on emulator. But these type are valid because the syscall only edit the correct number of byte.
    add #-4,r15 ; main version : unsigned char
    mov r15,r4
    add #-4,r15 ; minor version : unsigned char
    mov r15,r5
    add #-4,r15 ; release : unsigned short
    mov r15,r6
    add #-4,r15 ; build : unsigned short
    mov r15,r7
    ;call syscall
    mov.l #h'80010070,r2
    jsr   @r2
    mov #h'15,r0
    ;put os version into r6
    add #8,r15
    mov.b @r15,r6 ; minor version
    add #4,r15
    mov.b @r15,r0 ; main version
    add #4,r15
    shll8 r0 ; r0 = r0<<8
    add r0,r6
;reserved registers :
    ;r9 second param
    ;r6 OS version
; read and checks coords
    mov.b @r8,r7 ; r7 = Key's column
    mov.b @(1,r8),r0
    mov r0,r8 ; r8 = Key's row
    ;verify the row value  : 0 ≤ row ≤ 9
    mov #0, r0
    cmp/gt r8,r0 ; if r0 > r8 ⇒ if 0 > row
    bt NegativeEnd
    mov #9,r1
    cmp/gt r1,r8 ; if r8 > r1 ⇒ if row > 9
    bt NegativeEnd
    ;verify the column value  : 0 ≤ row ≤ 6
    cmp/gt r7,r0 ; if r0 > r7 ⇒ if 0 > column
    bt NegativeEnd
    mov #6,r1
    cmp/gt r1,r7 ; if r7 > r1 ⇒ if column > 6
    bt NegativeEnd
;check if os is > 2.02
    mov.w #h'0202,r0
    cmp/ge r0,r6 ; r0 ≤ r6
    bt SH4
;reserved registers :
    ;r9 second param
    ;r8 Key's row
    ;r7 Key's col
;SH3 part
    ;r6 = smask = 0x0003 << (( row %8)*2);
    mov r8,r0 ; row->r0
    and #7,r0 ; %8
    add r0,r0 ; *2
    mov #3,r6
    shld r0,r6 ; 3<<
    ;r5 = cmask = ~( 1 << ( row %8) );
    mov r8,r0 ; row->r0
    and #7,r0 ; %8
    mov #1,r5
    shld r0,r5 ; 1<<
    not r5,r5 ; ~
;reserved registers :
    ;r9 second param
    ;r8 Key's row
    ;r7 Key's col
    ;r6 smask
    ;r5 cmask
;Preparation of the gbr register
    mov.l #h'A4000100,r0
    ldc r0,gbr
;RowCond : if(row <8)
    mov #8,r0
    cmp/gt r8,r0 ; if r0>r8 ; row>=8
    bf rowCond_Else
;rowCond_begin
    ;*PORTB_CTRL = 0xAAAA ^ smask;
    mov r6,r0
    mov.w #h'AAAA,r1
    xor r1,r0
    mov.w r0,@(h'02,gbr)
    ;*PORTM_CTRL = (*PORTM_CTRL & 0xFF00 ) | 0x00AA;
    mov.w @(h'18,gbr),r0 ; *PORTM_CTRL->r0
    mov.w #h'FF00,r1
    and r1,r0 ;  *PORTM_CTRL & 0xFF00
    or #h'AA,r0 ;  | 0x00AA;
    mov.w r0,@(h'18,gbr)
    ;delay()
    bsr delay
    mov #-10,r4
    ;*PORTB = cmask;
    mov r5,r0
    mov.b r0,@(h'22,gbr) ;PORTB = cmask
    ;*PORTM = (*PORTM & 0xF0 ) | 0x0F;
    mov.b @(h'38,gbr),r0 ; *PORTM->r0
    and #h'F0,r0 ; *PORTM & 0xF0
    or #h'0F,r0 ;  | 0x0F;
    mov.b r0,@(h'38,gbr)
    bra rowCond_End
    nop
rowCond_Else:
    ; *PORTB_CTRL = 0xAAAA;
    mov.w #h'AAAA,r0
    mov.w r0,@(h'02,gbr)
    ; *PORTM_CTRL = ((*PORTM_CTRL & 0xFF00 ) | 0x00AA)  ^ smask;
    mov.w @(h'18,gbr),r0
    mov.w #h'FF00,r1
    and r1,r0 ;  *PORTM_CTRL & 0xFF00
    or #h'AA,r0 ;  | 0x00AA;
    xor r6,r0 ;  ^ smask;
    mov.b r0,@(h'18,gbr)
    ;delay()
    bsr delay
    mov #-10,r4 ;In the begin this was 5, but as the delay function is faster, i need to put more
    ;*PORTB = 0xFF;
    mov.b #h'ff,r0
    mov.b r0,@(h'22,gbr) ;PORTB = 0xFF
    ;*PORTM = (*PORTM & 0xF0 ) | cmask;
    mov.b @(h'38,gbr),r0
    and #h'F0,r0 ; *PORTM & 0xF0
    or r5,r0 ;  | cmask;
    mov.b r0,@(h'38,gbr)
rowCond_End:
    ;reserved registers :
        ;r9 second param
        ;r8 Key's row
        ;r7 Key's col
    ;delay()
    bsr delay
    mov #-10,r4
    ;result = (~(*PORTA))>>column & 1;
    mov.b @(h'20,gbr),r0
    not r0,r6 ; r6 = ~r0
    neg r7,r0 ; r0 = -column
    shld r0,r6 ; r6 = r6>>column
    mov.b #1,r0
    and r0,r6
    ;reserved registers :
        ;r9 second param
        ;r8 Key's row
        ;r7 Key's col
        ;r6 result
    ;delay()
    bsr delay
    mov #-10,r4
    ; *PORTB_CTRL = 0xAAAA;
    mov.w #h'AAAA,r0
    mov.w r0,@(h'02,gbr)
    ;*PORTM_CTRL = (*PORTM_CTRL & 0xFF00 ) | 0x00AA;
    mov.w @(h'18,gbr),r0
    mov.w #h'FF00,r1
    and r1,r0 ;  *PORTM_CTRL & 0xFF00
    or #h'AA,r0 ;  | 0x00AA;
    mov.w r0,@(h'18,gbr)
    ;delay()
    bsr delay
    mov #-10,r4
    ; *PORTB_CTRL = 0x5555;
    mov.w #h'5555,r0
    mov.w r0,@(h'02,gbr)
    ;*PORTM_CTRL = (*PORTM_CTRL & 0xFF00 ) | 0x0055;
    mov.w @(h'18,gbr),r0
    mov.w #h'FF00,r1
    and r1,r0 ;  *PORTM_CTRL & 0xFF00
    or #h'55,r0 ;  | 0x0055;
    mov.w r0,@(h'18,gbr)
    ;delay()
    bsr delay
    mov #-10,r4
    ;End of SH3 part
    bra AllEnd
    nop
SH4:
    ;Add 3 to the second param (if >0)to select the right wait time
    mov #0,r0
    cmp/gt r0,r9
    bf negatif2ndParam
    add #3,r9
negatif2ndParam:
    ;get the main keyboard regsiter address+1
    mov.l #H'A44B0001,r1
    mov r8,r0
    tst #1,r0 ;if row is even T=1 else T=0
    add r8,r1
    bt row_even ; Jump if T=1
    add #-2,r1
row_even:
    mov.b @r1,r0 ; The byte that contain the row data is now in R0
    mov #1,r1
    shld r7,r1 ; R9 now contain 1<<col
    tst r1,r0 ; if key is pressed T=0
    movt r0
    not r0,r0
    and #h'1,r0
    mov r0,r6
    bra AllEnd
    nop
NegativeEnd:
    mov #0,r6
;reserved registers :
    ;r9 second param
    ;r8 Key's row
    ;r7 Key's col
    ;r6 result
AllEnd:
    ;Wait the correct time to emulate old functions
    bsr delay
    mov r9,r4
    ;put result to return register : r0
    mov r6,r0
    ;take out data from stack
    mov.l   @r15+,r9
    mov.l   @r15+,r8
    mov.l   @r15+,r7
    mov.l   @r15+,r6
    mov.l   @r15+,r3
    mov.l   @r15+,r2
    mov.l   @r15+,r1
    lds.l   @r15+,pr
    rts
    nop



; delay : Wait a defined time
;param 1 (in r4) : slowMode : this determine the time this function will wait to emulate the olds functions
;      -n : number of loop
;      0 : fatest as possible (equivalent to -1)
;      1 : = duration of IsKeyDown function for SH3
;      2 : = duration of IsKeyUp function for SH3
;      3 : = duration of KeyDown function for SH3
;      4 : = duration of IsKeyDown function for SH4
;      5 : = duration of IsKeyUp function for SH4
;      6 : = duration of KeyDown function for SH4
delay:
;if r4<=0 then it's the number of loop
    mov #0,r0
    cmp/ge r0,r4
    bf LoopNumber
;Search the number of loop needed
    add r4,r4 ; *2
    mova loopNumbersList,r0
    add r4,r4 ; *2 because there is 4 byte per number of loop (this method take less space than use "MUL.L")
    add r4,r0
    mov.l @r0,r1
    bra target_loopBegin
    nop ; this nop is added because without the loopNumbersList is not divisible per 4
loopNumbersList:
    .data.l h'00000001   ;fastest (can't be under 1)
    .data.l h'000006F7   ;IsKeyDown SH3
    .data.l h'00012D1F   ;IsKeyUp SH3
    .data.l h'00000001   ;KeyDown SH3
    .data.l h'000005CD   ;IsKeyDown SH4
    .data.l h'0000F1A8   ;IsKeyUp SH4
    .data.l h'00000001   ;KeyDown SH4
LoopNumber:
    neg r4,r1
;Begin : r1 contain the number of loop
target_loopBegin:
    dt r1 ; decrement and test if(r1==0)
    bf target_loopBegin
    rts
    nop