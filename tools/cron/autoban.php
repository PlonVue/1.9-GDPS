<hr>
<?php
include "../../incl/lib/connection.php";
echo "<p>Initializing autoban</p>";
ob_flush();
flush();
$query = $db->prepare("SELECT starStars, coins, starDemon, starCoins FROM levels");
$query->execute();
$levelstuff = $query->fetchAll();
//counting stars
$stars = 0;
$demons = 0;
foreach($levelstuff as $level){
	$stars = $stars + $level["starStars"];
	if($level["starCoins"] != 0){
		$coins += $level["coins"];
	}
	if($level["starDemon"] != 0){
		$demons++;
	}
}
$query = $db->prepare("SELECT stars FROM mappacks");
$query->execute();
$result = $query->fetchAll();
//counting stars
echo "<h3>Stars based bans</h3>";
ob_flush();
flush();
foreach($result as $pack){
	$stars += $pack["stars"];
}
$quarter = floor($stars / 4);
$stars = $stars + 200 + $quarter;
$query = $db->prepare("SELECT userID, userName FROM users WHERE stars > :stars");
$query->execute([':stars' => $stars]);
$result = $query->fetchAll();
//banning ppl
foreach($result as $user){
	$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE userID = :id");
	$query->execute([':id' => $user["userID"]]);
	echo "<p>Banned ".htmlspecialchars($user["userName"],ENT_QUOTES)." - ".$user["userID"]."</p>";
}
//counting coins
$query = $db->prepare("SELECT coins FROM mappacks");
$query->execute();
$result = $query->fetchAll();

$total_levels = 21; //change this wen game has update!!
$goldCoins = ($total_levels * 3) + 5;
foreach($result as $pack){
	$goldCoins += $pack["coins"];
}

echo "<h3>Gold coins based bans</h3>";
ob_flush();
flush();
$query = $db->prepare("SELECT userID, userName FROM users WHERE coins > :goldCoins");
$query->execute([':goldCoins' => $goldCoins]);
$result = $query->fetchAll();
//banning ppl
foreach($result as $user){
	$query = $db->prepare("UPDATE users SET isBanned = '1', banReason = '<b>Autoban: Coins</b>' WHERE userID = :id");
	$query->execute([':id' => $user["userID"]]);
	echo "<p>Banned ".htmlspecialchars($user["userName"],ENT_QUOTES)." - ".$user["userID"]."</p>";
}

//counting usercoins
echo "<h3>User coins based bans</h3>";
ob_flush();
flush();
$quarter = floor($coins / 4);
$coins = $coins + 10 + $quarter;
$query = $db->prepare("SELECT userID, userName FROM users WHERE userCoins > :coins");
$query->execute([':coins' => $coins]);
$result = $query->fetchAll();
//banning ppl
foreach($result as $user){
	$query = $db->prepare("UPDATE users SET isBanned = '1', banReason = '<b>Autoban: Usercoins</b>' WHERE userID = :id");
	$query->execute([':id' => $user["userID"]]);
	echo "<p>Banned ".htmlspecialchars($user["userName"],ENT_QUOTES)." - ".$user["userID"]."</p>";
}
//counting demons
echo "<h3>Demons based bans</h3>";
ob_flush();
flush();
$quarter = floor($demons / 16);
$demons = $demons + 3 + $quarter;
$query = $db->prepare("SELECT userID, userName FROM users WHERE demons > :demons");
$query->execute([':demons' => $demons]);
$result = $query->fetchAll();
//banning ppl
foreach($result as $user){
	$query = $db->prepare("UPDATE users SET isBanned = '1', banReason = '<b>Autoban: Demons</b>' WHERE userID = :id");
	$query->execute([':id' => $user["userID"]]);
	echo "<p>Banned ".htmlspecialchars($user["userName"],ENT_QUOTES)." - ".$user["userID"]."</p>";
}
//banips
$query = $db->prepare("SELECT IP FROM bannedips");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$ip){
	$query = $db->prepare("UPDATE users SET isBanned = '1', banReason = '<b>Autoban: BannedIP</b>' WHERE IP LIKE CONCAT(:ip, '%')");
	$query->execute([':ip' => $ip["IP"]]);
}
echo "<p>Autoban finished</p>";
ob_flush();
flush();
//done
//echo "<hr>Banned everyone with over $stars stars and over $coins user coins and over $demons demons!<hr>done";
?>
<hr>