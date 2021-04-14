<?php

/**
 * Calculate the MiZip key
 * 
 * @param array $uid The UID divided in a byte array
 * @param array $xorKey The needed orkey found in the xortable array divided in a byte array
 * @param String $keyType Whether you need the key A or key B
 */
function calcKey(array $uid, array $xorKey, String $keyType)
{
	$order = [
		'A' => [0, 1, 2, 3, 0, 1],
		'B' => [2, 3, 0, 1, 2, 3]
	];

	$key = ''; // This will contain the calculated key

	foreach ($order[$keyType] as $i => $value) {
		// +1 BECAUSE THE ARRAY RETURNED BY HEX2BYTEARRAY STARTS AT 1
		$key .= sprintf("%02x", $uid[$value + 1] ^ $xorKey[$i + 1]);
	}
	return $key;
}

/**
 * Convert an hex value to a byte array
 * WATCH OUT: THIS $H1T GIVES YOU AN ARRAY THAT STARTS AT 1
 * SO THE FIRST INDEX IS 1 AND NOT 0
 * 
 * @param string $hexString The string to convert
 * @return array The byte array
 */
function hex2ByteArray(string $hexString)
{
	$string = hex2bin($hexString);
	return unpack('C*', $string);
}

/**
 * Generate the keys for a Mizip key
 * 
 * @param String $_uid The UID of the Mizip key
 */
function mizipKeyGen(String $_uid)
{
	/*
	[sector, xor for key A, xor for key B]
	*/
	$xortable = [
		['001', '09125a2589e5', 'F12C8453D821'],
		['002', 'AB75C937922F', '73E799FE3241'],
		['003', 'E27241AF2C09', 'AA4D137656AE'],
		['004', '317AB72F4490', 'B01327272DFD'],
	];

	// Convert the user-given UID into a Byte array
	$uid = hex2ByteArray($_uid);

	// Print a pretty table
	print('+-----+-----------------------------+' . PHP_EOL);
	print("| UID | $_uid                    |" . PHP_EOL);
	print('+-----+--------------+--------------+' . PHP_EOL);
	print('| SEC | key A        | key B        |' . PHP_EOL);
	print('+-----+--------------+--------------+' . PHP_EOL);
	print('| 000 | a0a1a2a3a4a5 | b4c132439eef |' . PHP_EOL);
	foreach ($xortable as $row) {
		// Set some vars to make the code more readable
		$sec = $row[0];
		$xorA = $row[1];
		$xorB = $row[2];
		// Calculate the keys
		$keyA = calcKey($uid, hex2ByteArray($xorA), 'A');
		$keyB = calcKey($uid, hex2ByteArray($xorB), 'B');
		// Print them
		printf('| %s | %s | %s |' . PHP_EOL, $sec, $keyA, $keyB);
	}
	print('+-----+--------------+--------------+');
}
