<?php
echo "Day 1: PHP is running" . "<br>";

echo "<br>";

$name = "Josiah";
$age = 22;
$program = "Information Technology";

echo "Name: " . $name . "<br>";
echo "Age: " . $age . "<br>";
echo "Program: " . $program;

echo "<br><br>";

if ($age >= 18) {
    echo "Status: Adult";
} else {
    echo "Status: Minor";
}

echo "<br><br>";

$subjects = ["Math", "English", "Science"];

foreach ($subjects as $subjects)
    echo $subjects . "<br>";

?>