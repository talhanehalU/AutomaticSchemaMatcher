<?php

session_start();

$schemaFilenames = $_SESSION['schemaFilenames'];

var_dump($schemaFilenames);

foreach($schemaFilenames as $schemaFilename)
{
    $schemaName = substr($schemaFilename, 0, strpos($schemaFilename, "."));
}

//creates object of CSV schema 
$file = fopen("sourceSchemas/Placement_Data_Full_Class.csv", "r");
$fileContent = file_get_contents("sourceSchemas/Placement_Data_Full_Class.csv");

//CREATE DATBASE statement
echo "<br><br>";
echo "CREATE DATABASE IF NOT EXISTS " . $schemaName . ";<br>";

$csvArray = str_getcsv($fileContent, ",");

var_dump($csvArray);

?>