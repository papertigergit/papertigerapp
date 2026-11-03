<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('diff'))
{

function diff($old, $new){
	if(!defined('maxlen')){
		$maxlen=0;	// was '': PHP 7 treated '' == 0 as true, which ends the recursion; PHP 8 does not
	}
        foreach($old as $oindex => $ovalue){
                $nkeys = array_keys($new, $ovalue);
                foreach($nkeys as $nindex){
                        $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                        if($matrix[$oindex][$nindex] > $maxlen){
                                $maxlen = $matrix[$oindex][$nindex];
                                $omax = $oindex + 1 - $maxlen;
                                $nmax = $nindex + 1 - $maxlen;
                        }   
                }    
        }   
        if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
        return array_merge(
                diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
                array_slice($new, $nmax, $maxlen),
                diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function htmlDiff($old, $new){
        $diff = diff(explode(' ', $old), explode(' ', $new));
	if(!defined('ret')){
		$ret='';
	}
        foreach($diff as $k){
                if(is_array($k))
                        $ret .= (!empty($k['d'])?implode(' ',$k['d'])." ":'').
                                (!empty($k['i'])?implode(' ',$k['i'])." ":'');
                else $ret .= $k . ' ';
        }   
        return $ret;
}



}
?>
