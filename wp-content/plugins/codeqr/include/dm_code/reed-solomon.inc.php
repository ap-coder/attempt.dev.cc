<?php
class Galois
{
    var $iOrder = -1;
    var $iPrimPol = -1;
    var $iLogTable = array();
    var $iInvLogTable = array();
    function __construct($aN, $aPol)
    {
        $this->iOrder   = 1 << $aN;
        $this->iPrimPol = $aPol;
        $this->InitLogTables();
    }
    function InitLogTables()
    {
        $this->iLogTable[0]    = 1 - $this->iOrder;
        $this->iInvLogTable[0] = 1;
        for ($i = 1; $i < $this->iOrder; ++$i) {
            $this->iInvLogTable[$i] = $this->iInvLogTable[$i - 1] << 1;
            if ($this->iInvLogTable[$i] >= $this->iOrder) {
                $this->iInvLogTable[$i] ^= $this->iPrimPol;
            }
            $this->iLogTable[$this->iInvLogTable[$i]] = $i;
        }
    }
    function Mul($a, $b)
    {
        if ($a == 0 || $b == 0) {
            return 0;
        } else {
            return $this->iInvLogTable[($this->iLogTable[$a] + $this->iLogTable[$b]) % ($this->iOrder - 1)];
        }
    }
}
class ReedSolomon
{
    var $iGalois;
    var $iC;
    var $iCodeWords = -1;
    function __construct($aWordSize, $aCodeWords)
    {
        $poly = array(
            6 => 67,
            8 => 301,
            10 => 1033,
            12 => 4201
        );
        $keys = array_keys($poly);
        if (!in_array($aWordSize, $keys)) {
            return false;
        }
        $this->iGalois    = new Galois($aWordSize, $poly[$aWordSize]);
        $this->iCodeWords = $aCodeWords;
        $this->InitGenPolynomial($aCodeWords);
    }
    function InitGenPolynomial($aN)
    {
        $this->iC    = array();
        $this->iC[0] = 1;
        for ($i = 1; $i <= $aN; ++$i) {
            $this->iC[$i] = 0;
        }
        for ($i = 1; $i <= $aN; ++$i) {
            $this->iC[$i] = $this->iC[$i - 1];
            $tmp          = $this->iGalois->iInvLogTable[$i];
            for ($j = $i - 1; $j >= 1; --$j) {
                $this->iC[$j] = $this->iC[$j - 1] ^ $this->iGalois->Mul($this->iC[$j], $tmp);
            }
            $this->iC[0] = $this->iGalois->Mul($this->iC[0], $tmp);
        }
    }
    function AppendCode(&$aData)
    {
        $n = count($aData);
        for ($i = $n; $i <= ($n + $this->iCodeWords); ++$i)
            $aData[$i] = 0;
        for ($i = 0; $i < $n; ++$i) {
            $k = $aData[$n] ^ $aData[$i];
            for ($j = 0; $j < $this->iCodeWords; ++$j) {
                $aData[$n + $j] = $aData[$n + $j + 1] ^ $this->iGalois->Mul($k, $this->iC[$this->iCodeWords - $j - 1]);
            }
        }
        unset($aData[$n + $this->iCodeWords]);
    }
}
?>
