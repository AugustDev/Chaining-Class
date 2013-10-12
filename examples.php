<?php

/* Contains all the functions required for chaining */
require_once("Chaining.class.php");


/* 
---------------------------------------------------------------------------------
	Example 1
	
	An example of how to apply a functions to the string that does and does not
	require additional parameters.
	
	The code of this example is result-equivalent to both of the following:
	
	#1
	$myString = "a <b>lovely</b> string <a href='#'>here</a>";
	$myString = htmlspecialchars($myString);
	$myString = str_replace("lovely", "beloved", $myString);
	echo $myString;
	
	#2
	echo str_replace("lovely", "beloved", htmlspecialchars($myString));
*/

$myString = "a <b>lovely</b> string <a href='#'>here</a>";

$chain = new Chaining($myString);
$chain->add("htmlspecialchars")
	  ->add("str_replace", array("lovely", "beloved", $chain->me()) );

echo $chain->getObject(); // getObject() and me() are completely equivalent

/* Output 
	a <b>beloved</b> string <a href='#'>here</a>	
*/


/*
---------------------------------------------------------------------------------
	Example 2
	
	An example of how to use custom functions with Chaining methods.
	It is just the same as using predefined functions.
	
	
*/
	function SameAsExplode($var)
	{
		return explode("|", $var);
	}
	
	function PrepareToPrint($data)
	{
		$output = "Name: ".$data[0].
				  "<br>Surname: ".$data[1].
				  "<br>Street: ".$data[2].
				  "<br>State: ".$data[3].
				  "<br>Country: ".$data[4].
				  "<br>Number: ".$data[5];
		return $output;
	}
	
	$variable = "Augustinas|Malinauskas|15 Peel st.|California|USA|0123456789";
	
	$chain = new Chaining();
	$chain->setObject($variable);
	
	$chain->add("SameAsExplode")
		  ->add("PrepareToPrint");
		  
	echo $chain->me();

/* Output

	Name: Augustinas
	Surname: Malinauskas
	Street: 15 Peel st.
	State: California
	Country: USA
	Number: 0123456789
*/	
	
	
/*
---------------------------------------------------------------------------------
	Example 3 
	
	An attempt to imitate somewhat real life situation where data from some kind
	of database is formatted before being print.
	
	Effect: functions htmlspecialchars and ucwords are applied to every string before printing,
	without htmlspecialchars function a simple html code could be inserted into messages
	resulting in redirect.
	
*/	

$mysql_data = array(
	array(
		'id' => 3370,
		'username' => 'august',
		'message' => 'how to increment a variable in PHP?',
		'signature' => 'There is no way to happiness, happiness is the way'
	),
	array(
		'id' => 3371,
		'username' => 'september',
		'message' => 'to increment a variable use: <b>$c++;<b/>',
		'signature' => '<meta http-equiv="refresh" content="0; url=http://example.com/xss_attack">'
	)
);

echo "<table>";
foreach($mysql_data as $entry)
{
	$chain = new Chaining($entry);
	$chain->add("array_map", array("htmlspecialchars", $chain->me()) )
		  ->add("array_map", array("ucwords", $chain->me()) );
		  
	$entry = $chain->getObject(); // same as $chain->me();

	echo '<tr>
			<td>'.$entry["id"].'</td>
			<td>'.$entry["username"].'</td>
		</tr>
		<tr>
			<td colspan="2">'.$entry["message"].'</td>
		</tr>
		<tr>
			<td colspan="2">Signature:'.$entry["signature"].'</td>
		</tr>';
}
echo "</table";

/* Output


	3370	August
	How To Increment A Variable In PHP?
	Signature:There Is No Way To Happiness, Happiness Is The Way
	
	3371	September
	To Increment A Variable Use: <b>$c++;<b/>
	Signature:<meta Http-equiv="refresh" Content="0; Url=http://example.com/xss_attack">

*/


/* 
---------------------------------------------------------------------------------
	Example 4 
	
	Using array as a working variable.
	Chaining method works just fine with arrays too. 
	The interesting thing in this example is that we used array as an input,
	but received formatted string as s result.
	
	Effect: Uppercase the first word letters in the array variables and merges them 
	using "," as separator. Appends a "." symbol at the end.
	
	Array map has prototype: array_map(myfunction, array), therefore we pass these
	variables in using a second parameter for add function:
		add("array_map", array("ucwords", $chain->me()) )
		
	$chain->me() contains the array we are working with.
*/

$myArray = array("name surname", "augustinas malinauskas", "paul gilbert");

$chain = new Chaining($myArray);
$chain->add("array_map", array("ucwords", $chain->me()) )
	  ->add("implode", array(", ", $chain->me()) )
	  ->stringAppend(".");

echo $chain->me();

/* Output

	Name Surname, Augustinas Malinauskas, Paul Gilbert.
*/


?>
