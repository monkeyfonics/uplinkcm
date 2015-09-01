<?php

function viite ($nr) {
	$f = array(7,3,1);
	$p=0;
	$t=0;
	for ($i = strlen($nr)-1;$i>=0;$i--) {
		$t += substr($nr,$i,1) * $f[$p];
		$p++;
		if ($p>2) $p=0;
	}
	return (10-$t%10)%10;
}

?>