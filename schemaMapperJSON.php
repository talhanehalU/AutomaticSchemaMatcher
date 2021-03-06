<?php
session_start();

/// edit to store in DB
?>

<?php include 'topbar.php' ?>

    <div class="container">
        <div class="row last-inf-dt">
        <br>
            <div class="col-sm-12">
                <h4 class="contact-title">View MySQL Mapped Sources Schemas</h4>
                <br><br>
                <?php

              //  include 'db.php';

                echo "<div class='embedCode'>";

                $schema = array();

                //reads schema filenames and displays them
                $schemaFilenames = $_SESSION['schemaFilenames'];

                echo "<br><br>";
                foreach($schemaFilenames as $schemaFilename)
                {
                echo $schemaFilename . "&nbsp;&nbsp;";
                }
                echo "<br><br>";



                //reads and parses JSON schema into PHP associative array
                function parseSchemaFile($schemaFilename)
                {
                    $filePath = "sourceSchemas/" . $schemaFilename;
                    $fileContent = file_get_contents($filePath);

                    $arr = json_decode($fileContent, "true");

                    return $arr;
                }

                //maps JSON schema name to MySQL CREATE DATABASE statement
                function createDatabase($sF, $index)
                {
                    include 'db.php';
                    global $schema;

                    $schemaName = substr($sF, 0, strpos($sF, "."));
                   // echo "<span id='keyword'>CREATE DATABASE IF NOT EXISTS </span>" . $schemaName . ";<br>";
                   $createDatabase =  "CREATE DATABASE IF NOT EXISTS " . $schemaName . ";";
                   $schema[$index] = $createDatabase;


                }

                //maps JSON object property name to MySQL table attribute
                function attributeNames($nK, $index, $sA)
                {
                    global $schema;

                    //echo $nK ." ";
                    if($nK != "dependencies")
                    {
                    $schema[$index] .= "$nK ";
 
                    }  
                    
                    /*
                    else
                    {
                        applyConstraints($sA, $nK, $index);
                    }
                    */
                }

                //maps JSON datatypes to MySQL datatypes
                function attributeTypes($sA, $index, $nK)
                {
                    
                    global $schema;

                 if($nK != "dependencies")
                 {   

                    $dataType = $sA["properties"][$nK]["type"];

                 switch($dataType)
                 {
                   case "string":
                   $schema[$index] .= "VARCHAR(255)";
                   break;

                   case "number":
                   $schema[$index] .= "FLOAT";
                   break;

                   case "integer":
                   $schema[$index] .= "INT";
                   break;

                   case "boolean":
                   $schema[$index] .= "BOOLEAN";
                  break;

                  case "array":

                    $keys  = array_keys($sA["properties"][$nK]["items"]); 

                    echo "ITEMS TYPE: "; //echo $sA["properties"][$nK]["items"]["type"][0];
                   // echo $sA["properties"][$nK]["items"][0]["type"];  
                   echo $sA["properties"][$nK]["items"][$keys[0]]["type"];

                  $schema[$index] .= "ID INT";
                  $schema[$index] .= "PRIMARY KEY (" . $sA["title"] . "ID," . $nK . "ID)";
                  $schema[$index] .= "CREATE TABLE ". $nK . "Array(<br>" . $nK . "ArrayID INT NOT NULL, ";
                  $schema[$index] .= $nK . " ";  

                  //datatype
                  $schema[$index] .= $sA["properties"][$nK]["items"][0]["type"] .",<br>";

                  //$sA["properties"][$nK]["properties"][$dNK[$i]]["type"]




                  $schema[$index] .= $nK . "ID INT";
                  $schema[$index] .= "PRIMARY KEY(" . $nK . "ArrayID));";
                  $schema[$index] .= "FOREIGN KEY (" . $nK ."ID) REFERENCES " . $sA["title"] . "(" . $nK . "ID)";
                  break;

                  case "object":
                  $schema[$index] .= "ID INT ";
                  $schema[$index] .= "FOREIGN KEY (" . $nK ."ID) REFERENCES " . $nK . "(" . $nK . "ID)";
                  $schema[$index] .= "CREATE TABLE ". $nK . "(<br>" . $nK . "ID INT NOT NULL, ";

                    
                    $dNK = array_keys($sA["properties"][$nK]["properties"]);

                    for($i=0; $i<count($dNK); $i++)
                    {
                        $schema[$index] .= $dNK[$i] . " ";

                        //echo "<br><br>dNK: " . $dNK[$i] . "<br><br>";

                        $schema[$index] .= $sA["properties"][$nK]["properties"][$dNK[$i]]["type"];
                    }
                  
                  
                  $schema[$index] .= "PRIMARY KEY(" . $nK . "ID," . $nK . ")";
                 
                  break;

                   default:
                   echo "Error: Invalid JSON data type";
                    }
                }    
                    echo "<br>";
                }

                //maps JSON constraints to MySQL constraints
                function applyConstraints($sA, $nK, $index)
                {
                    global $schema;

                    if($nK != "dependencies")
                    {
                        $dataType = $sA["properties"][$nK]["type"];

                        echo "datatype inside applyConstraints: " . $dataType;
    
                        //newcode
                        switch($dataType)
                        {
                            case "integer":
                            {
                                if(isset($sA["properties"][$nK]["minimum"]))
                                {
                                    //echo " <span id='keyword'>CONSTRAINT</span> minimumValue <span class='dtype'>CHECK</span> (" . $nK . ">=" . $sA["properties"][$nK]["minimum"] . ")<br>";
                                    $schema[$index] .= "CONSTRAINT minimumValue CHECK(" . $nK . ">=" . $sA["properties"][$nK]["minimum"] . ")";
                                }
    
    
                                if(isset($sA["properties"][$nK]["maximum"]))
                                {
                                    //echo " <span id='keyword'>CONSTRAINT</span> maximumValue <span class='dtype'>CHECK</span> (" . $nK . "<=" . $sA["properties"][$nK]["maximum"] . ")<br>";
                                    $schema[$index] .= "CONSTRAINT maximumValue CHECK(" . $nK . "<=" . $sA["properties"][$nK]["maximum"] . ")";
                                }
    
                                if(isset($sA["properties"][$nK]["exclusiveMinimum"]))
                                {
                                    $schema[$index] .= "CONSTRAINT exclusiveMinimum CHECK(" . $nK . " > " . $sA["properties"][$nK]["exclusiveMinimum"] . ")"; 
                                    //echo " <span id='keyword'>CONSTRAINT</span> exclusiveMinimum <span class='dtype'>CHECK</span> (" . $nK . ">" . $sA["properties"][$nK]["exclusiveMinimum"] . ")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["exclusiveMaximum"]))
                                {
                                    $schema[$index] .= "CONSTRAINT exclusiveMaximum CHECK(" . $nK . " < " . $sA["properties"][$nK]["exclusiveMaximum"] . ")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> exclusiveMaximum <span class='dtype'>CHECK</span> (" . $nK . "<" . $sA["properties"][$nK]["exclusiveMaximum"] . ")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["multipleOf"]))
                                {
                                    $schema[$index] .= "CONSTRAINT multipleOf CHECK(" . $nK . "%" .$sA["properties"][$nK]["multipleOf"] . "= 0)";
                                    //echo " <span id='keyword'>CONSTRAINT</span> multipleOf <span class='dtype'>CHECK</span> (" . $nK . "%" . $sA["properties"][$nK]["multipleOf"] . "= 0)<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["enum"]))
                                {
                                    $enumwa = 0;
    
                                    $schema[$index] .= " ENUM(";
    
                                    while($sA["properties"][$nK]["enum"][$enumwa])
                                    {
                                    $schema[$index] .=  $sA["properties"][$nK]["enum"][$enumwa] . ",";
                                    $enumwa++;
                                    }
                                    //echo "ENUM(" . $sA["properties"][$nK]["enum"] . ")<br>";
                                
                                    $schema[$index] .= ")";
                                }
    
                                if(isset($sA["properties"][$nK]["const"]))
                                { 
                                    $schema[$index] .= " CREATE TRIGGER `const` BEFORE INSERT ON" . $sA["title"] . "FOR EACH ROW BEGIN"; 
                                    $schema[$index] .= " IF NEW." . $nK . " <> 71 THEN "; 
                                    $schema[$index] .= " SIGNAL SQLSTATE '45000' SET message_text ='Only allowed value is " . $sA["properties"][$nK]["const"] . "';";
                                    $schema[$index] .= " END IF;"; 
                                    $schema[$index] .= " END ";
                                }
                                
    
                                break;
                            }
    
                            case "number":
                            {
                                if(isset($sA["properties"][$nK]["minimum"]))
                                {
                                    $schema[$index] .= "CONSTRAINT minimumValue CHECK(" . $nK . ">=" . $sA["properties"][$nK]["minimum"] . ")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> minimumValue <span class='dtype'>CHECK</span> (" . $nK . ">=" . $sA["properties"][$nK]["minimum"] . ")<br>";
                                }
    
    
                                if(isset($sA["properties"][$nK]["maximum"]))
                                {
                                    $schema[$index] .= "CONSTRAINT maximumValue CHECK(" . $nK . "<=" . $sA["properties"][$nK]["maximum"] . ")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> maximumValue <span class='dtype'>CHECK</span> (" . $nK . "<=" . $sA["properties"][$nK]["maximum"] . ")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["exclusiveMinimum"]))
                                {
                                    $schema[$index] .= "CONSTRAINT exclusiveMinimum CHECK(" . $nK . ">" . $sA["properties"][$nK]["exclusiveMinimum"] . ")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> exclusiveMinimum <span class='dtype'>CHECK</span> (" . $nK . ">" . $sA["properties"][$nK]["exclusiveMinimum"] . ")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["exclusiveMaximum"]))
                                {
                                    $schema[$index] .= "CONSTRAINT exclusiveMaximum CHECK(" . $nK . "<" . $sA["properties"][$nK]["exclusiveMaximum"] . ")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> exclusiveMaximum <span class='dtype'>CHECK</span> (" . $nK . "<" . $sA["properties"][$nK]["exclusiveMaximum"] . ")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["multipleOf"]))
                                {
                                    $schema[$index] .= "CONSTRAINT multipleOf CHECK(" . $nK . "%" . $sA["properties"][$nK]["multipleOf"] . "= 0)";
                                    //echo " <span id='keyword'>CONSTRAINT</span> multipleOf <span class='dtype'>CHECK</span> (" . $nK . "%" . $sA["properties"][$nK]["multipleOf"] . "= 0)<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["enum"]))
                                {
                                    $enumwa = 0;
    
                                    $schema[$index] .= "ENUM(";
    
                                    while($sA["properties"][$nK]["enum"][$enumwa])
                                    {
                                    $schema[$index] .=  $sA["properties"][$nK]["enum"][$enumwa] . ",";
                                    $enumwa++;
                                    }
                                    //echo "ENUM(" . $sA["properties"][$nK]["enum"] . ")<br>";
                                    $schema[$index] .= ")";
                                }
    
                                
    
                                if(isset($sA["properties"][$nK]["const"]))
                                { 
                                    $schema[$index] .= " CREATE TRIGGER `const` BEFORE INSERT ON" . $sA["title"] . "FOR EACH ROW BEGIN"; 
                                    $schema[$index] .= " IF NEW." . $nK . " <> 71 THEN "; 
                                    $schema[$index] .= " SIGNAL SQLSTATE '45000' SET message_text ='Only allowed value is " . $sA["properties"][$nK]["const"] . "';";
                                    $schema[$index] .= " END IF;"; 
                                    $schema[$index] .= " END ";
                                }
    
                                break;
                            }
    
                            case "string":
                            {
                                if(isset($sA["properties"][$nK]["maxLength"]))
                                {
                                    $schema[$index] .= "CONSTRAINT maxLength CHECK(LENGTH(" . $nK . ")<=" . $sA["properties"][$nK]["maxLength"] .")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> maxLength <span class='dtype'>CHECK</span> (LENGTH(" . $nK . ")<=" . $sA["properties"][$nK]["maxLength"] .")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["minLength"]))
                                {
                                    $schema[$index] .= "CONSTRAINT minLength CHECK(LENGTH(" . $nK . ")>=" . $sA["properties"][$nK]["minLength"] .")";
                                    //echo " <span id='keyword'>CONSTRAINT</span> minLength <span class='dtype'>CHECK</span> (LENGTH(" . $nK . ")>=" . $sA["properties"][$nK]["minLength"] .")<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["pattern"]))
                                {
                                    $schema[$index] .= "CONSTRAINT pattern CHECK(" . $nK . " LIKE '" . $sA["properties"][$nK]["pattern"] . "')";
                                    //echo " <span id='keyword'>CONSTRAINT</span> pattern <span class='dtype'>CHECK</span>(" . $nK . " LIKE '" . $sA["properties"][$nK]["pattern"] . "')<br>";
                                }
    
                                if(isset($sA["properties"][$nK]["enum"]))
                                {
                                    $enumwa = 0;
    
                                    $schema[$index] .= "ENUM(";
    
                                    while($sA["properties"][$nK]["enum"][$enumwa])
                                    {
                                    $schema[$index] .=  $sA["properties"][$nK]["enum"][$enumwa] . ",";
                                    $enumwa++;
                                    }
                                    //echo "ENUM(" . $sA["properties"][$nK]["enum"] . ")<br>";
                                    $schema[$index] .= ")";
                                }
    
                                if(isset($sA["properties"][$nK]["const"]))
                                { 
                                    $schema[$index] .= " CREATE TRIGGER `const` BEFORE INSERT ON" . $sA["title"] . "FOR EACH ROW BEGIN"; 
                                    $schema[$index] .= " IF NEW." . $nK . " <> 71 THEN "; 
                                    $schema[$index] .= " SIGNAL SQLSTATE '45000' SET message_text ='Only allowed value is " . $sA["properties"][$nK]["const"] . "';";
                                    $schema[$index] .= " END IF;"; 
                                    $schema[$index] .= " END ";
                                }
    
                                //fix enum
                                /*
                                if(isset($sA["properties"][$nK]["enum"]))
                                {
                                    echo " <span id='keyword'>CONSTRAINT</span> pattern <span class='dtype'>CHECK</span> (" . $nK . "LIKE '" . $sA["properties"][$nK]["pattern"] . "')<br>";
                                }
                                */
    
                                break;
                            }
    
                            case "array":
                            {
                                if(isset($sA["properties"][$nK]["maxItems"]))
                                {
                                    /*
                                    $schema[$index] .= "CONSTRAINT maxItems CHECK(COUNT(" . $nK .") <= " . $sA["properties"][$nK]["maxItems"] . ")";
                                    */
    
                                    $schema[$index] .= "CREATE TRIGGER maxItems";
                                    $schema[$index] .= "BEFORE INSERT";
                                    $schema[$index] .= "ON " . $sA["title"];
                                    $schema[$index] .= "FOR EACH ROW";
                                    $schema[$index] .= "BEGIN"; 
                                    $schema[$index] .= "SELECT COUNT(*) INTO @cnt FROM " . $sA["title"];
                                    $schema[$index] .= "IF @cnt >= ". $sA["properties"][$nK]["maxItems"] . " THEN ";
                                    $schema[$index] .= "SIGNAL SQLSTATE '45000'";
                                    $schema[$index] .= "SET MESSAGE_TEXT = 'An error occurred', MYSQL_ERRNO = 1001;"; 
                                    $schema[$index] .= " END IF ";
                                    $schema[$index] .= "END"; 
                                
                                 }
    
                                if(isset($sA["properties"][$nK]["minItems"]))
                                {
                                    //$schema[$index] .= "CONSTRAINT minItems CHECK(COUNT(" . $nK .") >= " . $sA["properties"][$nK]["minItems"] . ")";
                                
                                    $schema[$index] .= "CREATE TRIGGER minItems";
                                    $schema[$index] .= "BEFORE INSERT";
                                    $schema[$index] .= "ON " . $sA["title"];
                                    $schema[$index] .= "FOR EACH ROW";
                                    $schema[$index] .= "BEGIN"; 
                                    $schema[$index] .= "SELECT COUNT(*) INTO @cnt FROM " . $sA["title"];
                                    $schema[$index] .= "IF @cnt <= ". $sA["properties"][$nK]["minItems"] . " THEN ";
                                    $schema[$index] .= "SIGNAL SQLSTATE '45000'";
                                    $schema[$index] .= "SET MESSAGE_TEXT = 'An error occurred', MYSQL_ERRNO = 1001;"; 
                                    $schema[$index] .= " END IF ";
                                    $schema[$index] .= "END"; 
                                }
    
                                if(isset($sA["properties"][$nK]["maxContains"]))
                                {
                                    //$schema[$index] .= "CONSTRAINT maxContains CHECK(COUNT(" . $nK .") <= " . $sA["properties"][$nK]["maxContains"] . ")";
    
                                    $schema[$index] .= "CREATE TRIGGER maxContains";
                                    $schema[$index] .= "BEFORE INSERT";
                                    $schema[$index] .= "ON " . $sA["title"];
                                    $schema[$index] .= "FOR EACH ROW";
                                    $schema[$index] .= "BEGIN"; 
                                    $schema[$index] .= "SELECT COUNT(*) INTO @cnt FROM " . $sA["title"];
                                    $schema[$index] .= "IF @cnt >= ". $sA["properties"][$nK]["maxContains"] . " THEN ";
                                    $schema[$index] .= "SIGNAL SQLSTATE '45000'";
                                    $schema[$index] .= "SET MESSAGE_TEXT = 'An error occurred', MYSQL_ERRNO = 1001;"; 
                                    $schema[$index] .= " END IF ";
                                    $schema[$index] .= "END"; 
                                }
    
                                if(isset($sA["properties"][$nK]["minContains"]))
                                {
                                    //$schema[$index] .= "CONSTRAINT minContains CHECK(COUNT(" . $nK .") >= " . $sA["properties"][$nK]["minContains"] . ")";
                                    $schema[$index] .= "CREATE TRIGGER minContains";
                                    $schema[$index] .= "BEFORE INSERT";
                                    $schema[$index] .= "ON " . $sA["title"];
                                    $schema[$index] .= "FOR EACH ROW";
                                    $schema[$index] .= "BEGIN"; 
                                    $schema[$index] .= "SELECT COUNT(*) INTO @cnt FROM " . $sA["title"];
                                    $schema[$index] .= "IF @cnt <= ". $sA["properties"][$nK]["minContains"] . " THEN ";
                                    $schema[$index] .= "SIGNAL SQLSTATE '45000'";
                                    $schema[$index] .= "SET MESSAGE_TEXT = 'An error occurred', MYSQL_ERRNO = 1001;"; 
                                    $schema[$index] .= " END IF ";
                                    $schema[$index] .= "END"; 
                                
                                }
    
                                if(isset($sA["properties"][$nK]["uniqueItems"]))
                                {
                                    if($sA["properties"][$nK]["uniqueItems"] == "true")
                                        $schema[$index] .= "CONSTRAINT uniqueItems UNIQUE(" . $nK . ")";
    
                                    else
                                    echo "...";    
                                }
    
                                if(isset($sA["properties"][$nK]["enum"]))
                                {
                                    $enumwa = 0;
    
                                    $schema[$index] .= " ENUM(";
    
                                    while($sA["properties"][$nK]["enum"][$enumwa])
                                    {
                                    $schema[$index] .=  $sA["properties"][$nK]["enum"][$enumwa] . ",";
                                    $enumwa++;
                                    }
                                    //echo "ENUM(" . $sA["properties"][$nK]["enum"] . ")<br>";
                                    $schema[$index] .= ")";
                                }
    
                                
    
                                if(isset($sA["properties"][$nK]["const"]))
                                { 
                                    $schema[$index] .= " CREATE TRIGGER `const` BEFORE INSERT ON" . $sA["title"] . "FOR EACH ROW BEGIN"; 
                                    $schema[$index] .= " IF NEW." . $nK . " <> 71 THEN "; 
                                    $schema[$index] .= " SIGNAL SQLSTATE '45000' SET message_text ='Only allowed value is " . $sA["properties"][$nK]["const"] . "';";
                                    $schema[$index] .= " END IF;"; 
                                    $schema[$index] .= " END ";
                                }
    
                                break;
                            }
    
                            //work in progress
                            case "object":
                            {
                                if(isset($sA["properties"][$nK]["maxProperties"]))
                                {
                                    //$schema[$index] .= "CONSTRAINT maxProperties CHECK(COUNT(" . $nK . ") <= " . $sA["properties"][$nK]["maxProperties"] . ")";
                                
                                    $schema[$index] .= "CREATE TRIGGER maxProperties";
                                    $schema[$index] .= "BEFORE INSERT";
                                    $schema[$index] .= "ON " . $sA["title"];
                                    $schema[$index] .= "FOR EACH ROW";
                                    $schema[$index] .= "BEGIN"; 
                                    $schema[$index] .= "SELECT COUNT(*) INTO @cnt FROM " . $sA["title"];
                                    $schema[$index] .= "IF @cnt >= ". $sA["properties"][$nK]["maxProperties"] . " THEN ";
                                    $schema[$index] .= "SIGNAL SQLSTATE '45000'";
                                    $schema[$index] .= "SET MESSAGE_TEXT = 'An error occurred', MYSQL_ERRNO = 1001;"; 
                                    $schema[$index] .= " END IF ";
                                    $schema[$index] .= "END";
    
                                }
    
                                if(isset($sA["properties"][$nK]["minProperties"]))
                                {
                                    //$schema[$index] .= "CONSTRAINT minProperties CHECK(COUNT(" . $nK . ") >= " . $sA["properties"][$nK]["minProperties"] . ")";
                                
                                    $schema[$index] .= "CREATE TRIGGER minProperties";
                                    $schema[$index] .= "BEFORE INSERT";
                                    $schema[$index] .= "ON " . $sA["title"];
                                    $schema[$index] .= "FOR EACH ROW";
                                    $schema[$index] .= "BEGIN"; 
                                    $schema[$index] .= "SELECT COUNT(*) INTO @cnt FROM " . $sA["title"];
                                    $schema[$index] .= "IF @cnt <= ". $sA["properties"][$nK]["minProperties"] . " THEN ";
                                    $schema[$index] .= "SIGNAL SQLSTATE '45000'";
                                    $schema[$index] .= "SET MESSAGE_TEXT = 'An error occurred', MYSQL_ERRNO = 1001;"; 
                                    $schema[$index] .= " END IF ";
                                    $schema[$index] .= "END"; 
    
                                }
    
                                if(isset($sA["properties"][$nK]["required"]))
                                {
                                    //method 1
                                    $schema[$index] .= " NOT NULL ";
    
                                }
    
                                if(isset($sA["properties"][$nK]["dependencies"]))
                                {
                                    $dNK = array_keys($sA["properties"][$nK]["properties"]);

                                /*    
                                for($i=0; $i<count($dNK); $i++)
                                {}
                                */    
                                    //
                                    
                                    echo "<br><br>DEPENDENCIES: " . $sA["properties"][$nK]["dependencies"]["fuelMileage"][0] . "<br><br>";
                                    echo "<br><br>DEPENDENCIES: " . $sA["properties"][$nK]["dependencies"]["fuelMileage"][1] . "<br><br>";
                                    echo "inside dependencies constraint";

    
                                    //echo $sA["properties"][$nK]["dependencies"][$index] . " NOT NULL";
                                    
                                    /*
                                    for / foreach loop $index
    
                                    find the $sA["properties"][$nK]["dependencies"][$index]
                                    skip to after attriuute type & add the NOT NULL constraint 
                                    */
    
                                    $depedantRequiredwa = 0;
    
                                    //$schema[$index] .= "ENUM(";
    
                                    echo "inside dependant required...";
    
                                    while($sA["properties"][$nK]["dependencies"]["fuelMileage"][$depedantRequiredwa])
                                    {
                                        $attrNamePos = strpos($schema[$index], $sA["properties"][$nK]);
    
                                        $firstSpacePos = strpos($schema[$index], "\s", $attrNamePos);
    
                                        $secondSpacePos = strpos($schema[$index], "\s", $firstSpacePos);
    
                                        $schema[$secondSpacePos+1] = "NOT NULL";
    
                                    //$schema[$index] .=  $sA["properties"][$nK]["enum"][$enumwa] . ",";
                                    $depedantRequiredwa++;
                                    }
                                    //echo "ENUM(" . $sA["properties"][$nK]["enum"] . ")<br>";
                                    //$schema[$index] .= ")";
                                
                                }
    
                                if(isset($sA["properties"][$nK]["enum"]))
                                {
                                    $enumwa = 0;
    
                                    $schema[$index] .= "ENUM(";
    
                                    while($sA["properties"][$nK]["enum"][$enumwa])
                                    {
                                    $schema[$index] .=  $sA["properties"][$nK]["enum"][$enumwa] . ",";
                                    $enumwa++;
                                    }
                                    //echo "ENUM(" . $sA["properties"][$nK]["enum"] . ")<br>";
                                    $schema[$index] .= ")";
                                }
    
                                if(isset($sA["properties"][$nK]["const"]))
                                { 
                                    $schema[$index] .= " CREATE TRIGGER `const` BEFORE INSERT ON" . $sA["title"] . "FOR EACH ROW BEGIN"; 
                                    $schema[$index] .= " IF NEW." . $nK . " <> 71 THEN "; 
                                    $schema[$index] .= " SIGNAL SQLSTATE '45000' SET message_text ='Only allowed value is " . $sA["properties"][$nK]["const"] . "';";
                                    $schema[$index] .= " END IF;"; 
                                    $schema[$index] .= " END ";
                                }
    
                            break;
                            }
    
                            default:
                            echo "Array and Object types are being handled at the moment";
                        }
                    }

                }

                //defines MySQL table attributes for JSON properties
                function defineAttribute($sA, $index)
                {
                    $keys = array_keys($sA);
                    $nestedKeys = array_keys($sA["properties"]);

                    for($i=0; $i<count($nestedKeys); $i++)
                    {
                        attributeNames($nestedKeys[$i], $index, $sA);
                        attributeTypes($sA, $index, $nestedKeys[$i]);
                        applyConstraints($sA, $nestedKeys[$i], $index);
                    }    
                }

                //maps JSON object name to MySQL CREATE TABLE statement
                function createTables($sA, $index)
                {

                    global $schema;

                    if(array_keys($sA["properties"]) != "dependencies")
                    {
                        $schema[$index] .= "CREATE TABLE " . $sA["title"] . "("; 

                        //for PK of table
                        $schema[$index] .= $sA["title"] . "ID INT NOT NULL ";
                

                    defineAttribute($sA, $index);

                    //for PK of table
                    $schema[$index] .= "PRIMARY KEY(" . $sA["title"] . "ID)";

                    $n2K = array_keys($sA["properties"]);

                    //for($k=0; $k<count($n2K); $i++)
                    for($k=0; $k<count($n2K); $k++)
                    {
                        $dataType = $sA["properties"][$n2K[$k]]["type"];

            

                        echo "<br><br>Index: " . $index . "<br><br>";
                        echo "<br><br>Datatype: " . $dataType . "<br><br>";
    
    
                        if($dataType == "array" || $dataType == "object")
                        {
                            //$pos = strpos($schema[$index], ");");
                        }
    
                        else
                        {
                            $schema[$index] .= ");"; 
                        }
                    }

                }
            }
                //new function to ouput the SCHEMA string
                function displaySchema($schema)
                {
                    
                    for($s=0; $s<count($schema); $s++)
                    {
                        echo "<br><br> Schema Array Element " . $s;
                        echo "<br><br>" . $schema[$s] . "<br><br>";
                        echo "<br><br> END Schema Array Element " . $s;
                    }
                    
                }

                //maps JSON schema to MySQL schema
                function mapToSQL($sF, $index)
                {
                    global $schema;
                    
                    $fileExtension = substr($sF, strpos($sF, ".")+1);

                    if($fileExtension === "json")
                    {
                       

                    createDatabase($sF, $index);
                    $schemaArray = parseSchemaFile($sF);
                    createTables($schemaArray, $index);

                    //insertSchemaDB($schema[$index]);

                    echo "<br><br><br><br>";
                    echo $schema[$index];    
                    echo "<br><br><br><br>";
                    }
                }

                echo "Schema Files Count: " . count($schemaFilenames);

                //calling function that maps JSON schema to MySQL schema on each schema file
                for($i=0; $i<count($schemaFilenames); $i++)
                {
                    echo "<br><br>";
                    mapToSQL($schemaFilenames[$i], $i);
                    echo "<br><br>";

                   
                }

                displaySchema($schema);

                echo "</div>";

                ?>
                <!---->
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->

<?php include 'footer.php' ?>