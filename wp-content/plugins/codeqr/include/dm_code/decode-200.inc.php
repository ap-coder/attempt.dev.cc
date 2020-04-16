<?php
 class Decode_200 { var $iSymbols = array(),$iSymbolIdx=0,$iSymbolLen=0; var $iBuffer=array(),$iBufferIdx=0; var $iValues=array(),$iValuesIdx=0; var $iCurrentEncoding = ENCODING_ASCII; var $iDebug=false; function Decode_200($aSymbols=array()) { $this->iSymbols = $aSymbols; $this->iSymbolLen = count($this->iSymbols); } function Init($aSymbols) { $this->iSymbols = $aSymbols; $this->iSymbolLen = count($this->iSymbols); $this->iSymbolIdx = 0; $this->iBuffer = array(); $this->iBufferIdx=0; $this->iValues=array(); $this->iValuesIdx=0; $this->iCurrentEncoding = ENCODING_ASCII; } function PutBuffer($aVal) { $this->iBuffer[$this->iBufferIdx++] = intval($aVal); } function PutVal($aVal){ $this->iValues[$this->iValuesIdx++] = intval($aVal); } function FlushBuffer() { switch( $this->iCurrentEncoding ) { case ENCODING_ASCII: $this->Decode_ASCII(); break; case ENCODING_TEXT: $this->Decode_C40Text(true); break; case ENCODING_C40: $this->Decode_C40Text(); break; case ENCODING_BASE256: $n = count($this->iBuffer); for($i=0; $i < $n; ++$i ) { $this->PutVal($this->iBuffer[$i]); } break; case ENCODING_X12: $this->Decode_X12(); break; case ENCODING_EDIFACT: $this->Decode_EDIFACT(); break; } $this->iBuffer = array(); $this->iBufferIdx = 0; } function GetSymbol($aPeek=false) { if( $this->iSymbolIdx >= $this->iSymbolLen ) { return false; } else { $v = $this->iSymbols[$this->iSymbolIdx]; if( !$aPeek ) $this->iSymbolIdx++; return intval($v); } } function Decode_C40Text($aText=false) { $n = $this->iBufferIdx; $idx=0; while( $idx < $n ) { $v = $this->iBuffer[$idx++]; if( $v == 0 && ($idx == $n) ) return; if( $n % 3 ) { die("Extra code values found when decoding C40 (must be a multiple of 3)\n"); } $shift=0; switch( $v ) { case 0: $shift = 1; $v = $this->iBuffer[$idx++]; break; case 1: $shift = 2; $v = $this->iBuffer[$idx++]; break; case 2: $shift = 3; $v = $this->iBuffer[$idx++]; break; } switch( $shift ) { case 1: if( $v > 31 ) die("Illegal symbol value for shift 1 C40/TEXT ($v)\n"); else $this->PutVal($v); break; case 2: if( $v == 30 ) { $v = $this->iBuffer[$idx++]; $v += 128; $this->PutVal($v); } else { if( $v >= 0 && $v <= 14 ) $v += 33; elseif( $v >= 15 && $v <= 21 ) $v += 43; elseif( $v >= 22 && $v <= 26 ) $v += 69; elseif( $v == 27 ) { $this->PutVal(ord('~')); $v = ord('1'); } else die("Illegal symbol value for shift 2 C40/TEXT ($v)\n"); $this->PutVal($v); } break; case 3: if( $v > 31 ) die("Illegal symbol value for shift 3 C40 > 31 ($v)\n"); if( $aText ) { if( $v === 0 ) $v = 96; elseif( $v >= 1 && $v <= 26 ) $v += 64; else $v += 96; } else { $v += 96; } $this->PutVal($v); break; default: if( $v == 3 ) $v = 32; elseif( $v >= 4 && $v <= 13 ) $v += 44; elseif( $v >= 14 && $v <= 39 ) $v += $aText ? 83 : 51; else die("Illegal symbol value for C40/TEXT basic > 39 ($v)\n"); $this->PutVal($v); break; } } } function Decode_ASCII() { $n = $this->iBufferIdx; $idx=0; while( $idx < $n ) { $v = $this->iBuffer[$idx++]; if( $v >= 130 && $v <= 229 ) { $v -= 130; $d1 = floor($v/10); $this->PutVal($d1+48); $this->PutVal(($v - $d1*10)+48); } elseif( $v <= 128) { $this->PutVal($v-1); } elseif( $v == 235 ) { $v = $this->iBuffer[$idx++]+128; $this->PutVal($v); } else { die("Illegal code value in ASCII encoding ($v)\n"); } } } function Decode_X12() { $n = $this->iBufferIdx; $idx=0; while( $idx < $n ) { $v = $this->iBuffer[$idx++]; if( $v == 0 ) $this->PutVal(13); elseif( $v == 1 ) $this->PutVal(42); elseif( $v == 2 ) $this->PutVal(62); elseif( $v == 3 ) $this->PutVal(32); elseif( $v >= 4 && $v <= 13 ) $this->PutVal($v+44); elseif( $v >= 14 && $v <= 39 ) $this->PutVal($v+51); else die("Illegal code value in X12 endcoding ($v)}\n"); } } function Decode() { $latchTo = array(231 => ENCODING_BASE256, 230 => ENCODING_C40, 239 => ENCODING_TEXT, 238 => ENCODING_X12 , 240 => ENCODING_EDIFACT ); $v = $this->GetSymbol(); $this->iCurrentEncoding = ENCODING_ASCII; while( $v !== false && !($v==129 && $this->iSymbolIdx==$this->iSymbolLen) ) { switch( $this->iCurrentEncoding ) { case ENCODING_ASCII: if( $v == 129 ) { $this->FlushBuffer(); return; } if( array_key_exists($v,$latchTo) ) { $this->FlushBuffer(); $this->iCurrentEncoding = $latchTo[$v]; } else { $this->PutBuffer($v); } break; case ENCODING_TEXT: case ENCODING_C40: if( $v == 254 ) { $this->FlushBuffer(); $this->iCurrentEncoding = ENCODING_ASCII; } else { $v2 = $this->GetSymbol(); if( $v2 === false ) { $this->FlushBuffer(); $this->PutVal($v-1); } else { $t = $v*256 + $v2 - 1; $v = floor($t/1600); $this->PutBuffer($v); $t -= $v*1600; $v = floor($t/40); $this->PutBuffer($v); $t -= $v*40; $this->PutBuffer($t); } } break; case ENCODING_BASE256: $rand = ((149*($this->iSymbolIdx)) % 255) + 1; $v -= $rand; if( $v < 0 ) $v += 256; if( $v >= 250 ) { $v2 = $this->GetSymbol(); $rand = ((149*($this->iSymbolIdx)) % 255) + 1; $v2 -= $rand; if( $v2 < 0 ) $v2 += 256; $cnt = ($v-249)*250 + $v2; } else $cnt = $v; while( $cnt > 0 ) { $v = $this->GetSymbol(); $rand = ((149*($this->iSymbolIdx)) % 255) + 1; $v -= $rand; if( $v < 0 ) $v += 256; $this->PutBuffer($v); $cnt--; } $this->FlushBuffer(); $this->iCurrentEncoding = ENCODING_ASCII; break; case ENCODING_X12: if( $v == 254 ) { $this->FlushBuffer(); $this->iCurrentEncoding = ENCODING_ASCII; } else { $v2 = $this->GetSymbol(); if( $v2 === false ) { $this->FlushBuffer(); $this->PutVal($v-1); } else { $t = $v*256 + $v2 - 1; $v = floor($t/1600); $this->PutBuffer($v); $t -= $v*1600; $v = floor($t/40); $this->PutBuffer($v); $t -= $v*40; $this->PutBuffer($t); } } break; case ENCODING_EDIFACT: die("EDIFACT decoding not yet implemented.\n"); break; } $v = $this->GetSymbol(); } $this->FlushBuffer(); } function toString() { $t = trim(implode('',array_map('chr',$this->iValues))); return $t; } } ?>
