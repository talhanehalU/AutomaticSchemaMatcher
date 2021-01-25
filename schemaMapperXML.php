<?php

session_start();

include 'topbar.php';

?> 

    <div class="container">
        <div class="row last-inf-dt">
        <br>
            <div class="col-sm-12">
                <h4 class="contact-title">View MySQL Mapped Sources Schemas</h4>
                <br><br>
                <?php

                    echo "<div class='embedCode'>";
                    
                    $schemaFilenames = $_SESSION['schemaFilenames'];

                    echo "<br><br>";
                    foreach($schemaFilenames as $schemaFilename)
                    {
                    echo $schemaFilename . "&nbsp;&nbsp;";
                    }
                    echo "<br><br>";

                    function createDatabase($sF)
                    {
                        $schemaName = substr($sF, 0, strpos($sF, "."));
                        echo "<span id='keyword'>CREATE DATABASE IF NOT EXISTS</span> " . $schemaName . ";<br>";
                    }

                    function parseSchemaFile($sF)
                    {
                        $filePath = "sourceSchemas/" . $sF;
                        $fileString = file_get_contents($filePath);
                    
                        $doc = new DOMDocument();
                        $doc->loadXML(mb_convert_encoding($fileString, 'utf-8', mb_detect_encoding($fileString)));

                        $xpathObj = new DOMXPath($doc);
                        $xpathObj->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

                        return $xpathObj;
                    }

                    function attributeNames($cA)
                    {
                        echo $cA->getAttribute('name');
                    }

                    function attributeTypes($d, $cA)
                    {
                        $dataType = $cA->getAttribute('type');

                        switch ($dataType)
                        {
                            case "xs:int":
                                echo " <span class='dtype'>INT</span>";
                                break;

                            case "xs:string":
                                echo " <span class='dtype'>VARCHAR(<span class='size'>255</span>)</span>";
                                break;

                            //need to work out constraint vs sql parameter (24)    
                            case "xs:float":
                                echo " <span class='dtype'>FLOAT(<span class='size'>24</span>)</span>";
                                break;
                        
                            //need to work out constraint vs sql parameter (24,53)
                            case "xs:double":
                                echo " <span class='dtype'>DOUBLE(<span class='size'>24,53</span>)</span>";
                                break;    

                            //need to work out constraint vs sql parameter (65,30)    
                            case "xs:decimal":
                                echo " <span class='dtype'>DECIMAL(<span class='size'>65,30</span>)</span>";
                                break;

                            case "xs:boolean":
                                echo " <span class='dtype'>BOOLEAN</span>";
                                break;

                            //need to find xs:duration datatype equivalent in SQL
                        
                            case "xs:dateTime":
                                echo " <span class='dtype'>DATETIME</span>";
                                break;

                            case "xs:date":
                                echo " <span class='dtype'>DATE</span>";
                                break;

                            case "xs:time":
                                echo " <span class='dtype'>TIME</span>";
                                break;

                            case "xs:hexBinary":
                                echo " <span class='dtype'>VARBINARY</span>";
                                break;
                            
                            case "xs:base64Binary":
                                echo " <span class='dtype'>VARBINARY</span>";
                                break;
                    
                            case "xs:anyURI":
                                echo "no SQL datatype matches xs:anyURI";    
                                break;

                            case "xs:QName":
                                echo "no SQL datatype matches xs:QName";    
                                break;
                                        
                            case "xs:NOTATION":
                                echo "no SQL datatype matches xs:NOTATION";    
                                break;

                            default:
                            applyConstraints($d, $dataType, $cA);   

                        }
                    }

                    function defineAttribute($d, $eD)
                    {
                        $tableAttrs = $d->evaluate("xs:complexType/xs:sequence/xs:element", $eD);

                        for($i=0; $i < $tableAttrs->length; $i++)
                        {
                            $currentAttr = $tableAttrs->item($i);

                            attributeNames($currentAttr);
                            attributeTypes($d, $currentAttr);
                            echo "<br>";
                        }    
                    }

                    function stringConstraints($currentAttr, $sT, $constraint)
                    {
                        $allowedConstraints = array("xs:length", "xs:minLength" , "xs:maxLength", "xs:pattern", "xs:enumeration", "xs:whiteSpace", "xs:assertions");

                        //direct apply switch
                        $i=0;

                        if(in_array($constraint, $allowedConstraints))
                        {
                            switch($constraint)
                            {
                                case "xs:length":
                                    echo "(<span class='size'>" . $sT->firstChild->firstChild->getAttribute("value") . "</span>)<br>";
                                break;  
                                
                                case "xs:minLength":
                                    echo "(<span id='keyword'>CONSTRAINT</span> minLength <span id='keyword'>CHECK</span>(LENGTH(" . $currentAttr->getAttribute('name') .  ") >= " . $sT->firstChild->firstChild->getAttribute("value") . ")";
                                break;

                                case "xs:maxLength":
                                    echo "(<span id='keyword'>CONSTRAINT</span> maxLength <span id='keyword'>CHECK</span>(LENGTH(" . $currentAttr->getAttribute('name') .  ") <= " . $sT->firstChild->firstChild->getAttribute("value") . ")";
                                break;
                                
                                case "xs:pattern":
                                    echo "<span id='keyword'>CONSTRAINT</span> pattern <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " LIKE '" . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                break;
                                
                                case "xs:enumeration":
                                { 
                                    $enums = $sT->firstChild->childNodes;

                                    echo "ENUM(";

                                    foreach($enums as $enum)
                                    {
                                        echo "'" . $enum->getAttribute("value") . "', ";
                                    }

                                    echo ")";

                                break;
                                }
                                
                                case "xs:whiteSpace":
                                    echo "whiteSpace Constraint";
                                    break;    

                                case "xs:assertions":
                                    echo "assertions Constraint"; 
                                    break;   

                                default:
                                echo "to be handled soon!";    
                            }
                            $i++;
                        }

                        else
                        {
                            echo "The constraint is not applicable on xs:string type";
                        }
                    }


                    /*
                    function floatConstraints($currentAttr, $sT, $constraint)
                    {
                        $allowedConstraints = array("xs:pattern", "xs:enumeration", "xs:whiteSpace", "xs:maxInclusive", "xs:maxExclusive", "xs:minInclusive", "xs:minExclusive", "xs:assertions");
                    
                        //directly apply switch
                        $i=0;

                        if(in_array($constraint, $allowedConstraints))
                        {
                            switch($constraint)
                            {
                                case "xs:pattern":
                                { 
                                    echo "<span id='keyword'>CONSTRAINT</span> pattern <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " LIKE '" . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                    break;
                                }
                                
                                case "xs:enumeration":
                                {
                                    $enums = $sT->firstChild->childNodes;

                                    echo "ENUM(";

                                    foreach($enums as $enum)
                                    {
                                        echo "'" . $enum->getAttribute("value") . "', ";
                                    }

                                    echo ")";

                                    //echo "<span id='keyword'>CONSTRAINT</span> enum <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " LIKE '" . $sT->firstChild->firstChild->getAttribute("value")  . "')";  
                                    break;
                                }
                                
                                case "xs:whiteSpace":
                                {
                                    //need to figure out
                                }
                                
                                case "xs:maxInclusive":
                                {
                                    echo "<span id='keyword'>CONSTRAINT</span> maxInclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " <= " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                    break; 
                                }
                                
                                case "xs:maxExclusive":
                                {
                                    echo "<span id='keyword'>CONSTRAINT</span> maxExclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " < " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                    break; 
                                }
                                
                                case "xs:minInclusive":
                                {
                                    echo "<span id='keyword'>CONSTRAINT</span> minInclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " >= " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                    break; 
                                }
                                
                                case "xs:minExclusive":
                                {
                                    echo "<span id='keyword'>CONSTRAINT</span> minExclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " > " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                    break; 
                                }

                                case "xs:assertions":
                                {
                                    break;
                                }    

                                    default:
                                echo "to be handled soon!";    
                            }
                            $i++;

                            }
                        }
                        */            
            
                        function floatDoubleConstraints($currentAttr, $sT, $constraint)
                        {
                            $allowedConstraints = array("xs:pattern", "xs:enumeration", "xs:whiteSpace", "xs:maxInclusive", "xs:maxExclusive", "xs:minInclusive", "xs:minExclusive", "xs:assertions");
                        
                            //directly apply switch
                            $i=0;

                            if(in_array($constraint, $allowedConstraints))
                            {
                                switch($constraint)
                                {
                                    case "xs:pattern":
                                    { 
                                        echo "<span id='keyword'>CONSTRAINT</span> pattern <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " LIKE '" . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                        break;
                                    }
                                    
                                    case "xs:enumeration":
                                    {
                                        $enums = $sT->firstChild->childNodes;

                                        echo "ENUM(";

                                        foreach($enums as $enum)
                                        {
                                            echo "'" . $enum->getAttribute("value") . "', ";
                                        }

                                        echo ")";

                                        //echo "<span id='keyword'>CONSTRAINT</span> enum <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " LIKE '" . $sT->firstChild->firstChild->getAttribute("value")  . "')";  
                                        break;
                                    }
                                    
                                    case "xs:whiteSpace":
                                    {
                                        //need to figure out
                                    }
                                    
                                    case "xs:maxInclusive":
                                    {
                                        echo "<span id='keyword'>CONSTRAINT</span> maxInclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " <= " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                        break; 
                                    }
                                    
                                    case "xs:maxExclusive":
                                    {
                                        echo "<span id='keyword'>CONSTRAINT</span> maxExclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " < " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                        break; 
                                    }
                                    
                                    case "xs:minInclusive":
                                    {
                                        echo "<span id='keyword'>CONSTRAINT</span> minInclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " >= " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                        break; 
                                    }
                                    
                                    case "xs:minExclusive":
                                    {
                                        echo "<span id='keyword'>CONSTRAINT</span> minExclusive <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " > " . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                        break; 
                                    }

                                    case "xs:assertions":
                                    {
                                        break;
                                    }    

                                        default:
                                    echo "to be handled soon!";    
                                }
                                $i++;

                                }
                            }            
                            
                            function decimalConstraints($currentAttr, $sT, $constraint)
                            {
                                $allowedConstraints = array("xs:totalDigits", "xs:fractionDigits", "xs:pattern", "xs:whiteSpace", "xs:enumeration", "xs:maxInclusive", "xs:maxExclusive", "xs:minInclusive", "xs:minExclusive", "xs:assertions");

                                $i=0;

                                if(in_array($constraint, $allowedConstraints))
                                {
                                    switch($constraint)
                                    {
                                        case "xs:totalDigits":
                                            echo "(" . $sT->firstChild->firstChild->getAttribute("value") . ")";
                                            break;

                                        case "xs:fractionDigits":
                                            break;
                                        
                                        case "xs:pattern":
                                            break;
                                            
                                        case "xs:whiteSpace":
                                            break;
                                            
                                        case "xs:enumeration":
                                            break;
                                            
                                        case "xs:maxInclusive":
                                            break;
                                            
                                        case "xs:maxExclusive":
                                            break;
                                            
                                        case "xs:minInclusive":
                                            break;
                                            
                                        case "xs:minExclusive":
                                            break;
                                            
                                        case "xs:assertions":
                                            break;
                                            
                                        default:
                                            echo "default case to be handled soon";     
                                    }

                                    $i++;

                                }


                            }

                            function booleanConstraints($currentAttr, $sT, $constraint)
                            {
                                $allowedConstraints = array("xs:pattern", "xs:whiteSpace", "xs:assertions");

                                //direct apply switch
                                $i=0;

                                if(in_array($constraint, $allowedConstraints))
                                {
                                    switch($constraint)
                                    {
                                        case "xs:pattern":
                                            { 
                                                echo "<span id='keyword'>CONSTRAINT</span> pattern <span id='keyword'>CHECK</span>(" . $currentAttr->getAttribute('name') . " LIKE '" . $sT->firstChild->firstChild->getAttribute("value")  . "')";
                                            break;
                                            }    
                    
                                            default:
                                            echo "to be handled soon!";    
                                        }
                                        $i++;
                                    }
                    
                                    else
                                    {
                                        echo "The constraint is not applicable on xs:boolean type";
                                    }
                            }

                            function applyConstraints($dX, $dT, $cA)
                            {

                                $dataType = substr($dT, strpos($dT, ":")+1);

                                $simpleType = $dX->evaluate("/xs:schema/xs:element/xs:complexType/xs:sequence/xs:simpleType");

                                foreach($simpleType as $sT)
                                {

                                 echo "<br><br>SimpleType Name: " . $sT->getAttribute("name");   
                                 echo "<br><br>DataType type= " . $dataType; 
                                 

                                if($sT->getAttribute("name") == $dataType)
                                {

                                    $base = $sT->childNodes->item(1)->getAttribute("base");;

                                    echo "BASE " . $base;

                                    // $facet = $sT->firstChild->firstChild->nodeName;

                                    $facets = $sT->childNodes->item(0)->childNodes;
                                    
                                    for($i=0; $i < $facets->length; $i++)
                                    {
                                        $facet = $facets->item($facets->length - ($i+1))->nodeName;

                                        switch($base)
                                        {
                                            case "xs:int":
                                                intConstraints($cA, $sT , $facet);
                                                break;
                                    
                                            case "xs:string":
                                                stringConstraints($cA, $sT , $facet);
                                                break;
                                    
                                            //need to work out constraint vs sql parameter (24)    
                                            case "xs:float":
                                                floatDoubleConstraints($cA, $sT , $facet);
                                                break;
                                    
                                            //need to work out constraint vs sql parameter (24)
                                            case "xs:double":
                                                floatDoubleConstraints($cA, $sT, $facet);
                                                break;    
                                    
                                            //need to work out constraint vs sql parameter (65,30)    
                                            case "xs:decimal":
                                                decimalConstraints($cA, $sT , $facet);
                                                break;
                                    
                                            case "xs:boolean":
                                                booleanConstraints($cA, $sT , $facet);
                                                break;

                                            case "xs:duration": 
                                                dateTimeConstraints($cA, $sT , $facet);
                                                break;  
                                        
                                            case "xs:dateTime":
                                                dateTimeConstriants($cA, $sT , $facet);
                                                break;

                                            case "xs:time":
                                                dateTimeConstraints($cA, $sT , $facet);
                                                break;      
                                    
                                            case "xs:date":
                                                dateTimeConstriants($cA, $sT , $facet);
                                                break;
                                    
                                            case "xs:time":
                                                dateTimeConstriants($cA, $sT , $facet);
                                                break;

                                            case "xs:gYearMonth":
                                                dateTimeConstraints($cA, $sT , $facet);
                                                break;
                                                
                                            case "xs:gYear":
                                                dateTimeConstraints($cA, $sT , $facet);
                                                break;
                                            
                                            case "xs:gMonthDay":
                                                dateTimeConstraints($cA, $sT , $facet);
                                                break;      
                                                
                                            case "xs:gDay":
                                                dateTimeConstraints($cA, $sT , $facet);
                                                break;
                                                
                                            case "xs:gMonth":
                                                dateTimeConstaints($cA, $sT , $facet);    
                                    
                                            case "xs:hexBinary":
                                                otherConstraints($cA, $sT , $facet);
                                                break;
                                        
                                            case "xs:base64Binary":
                                                otherConstraints($cA, $sT , $facet);
                                                break;
                                            
                                            case "xs:anyURI":
                                                otherConstraints($cA, $sT , $facet);    
                                                break;
                                    
                                            case "xs:QName":
                                                otherConstraints($cA, $sT , $facet);    
                                                break;
                                                
                                            case "xs:NOTATION":
                                                otherConstraints($cA, $sT , $facet);    
                                                break;

                                            default:
                                            echo "default case";
                                        }
                                    }



                                    //$facet = $sT->firstChild->firstChild->nodeName;
                /*                   
                                    switch($facet)
                                    {
                                        case "xs:length":
                                            echo "(<span class='size'>" . $sT->firstChild->firstChild->getAttribute("value") . "</span>)<br>";
                                        break;  
                                        
                                        case "xs:maxLength":

                                        break;

                                        default:
                                        echo "to be handled soon!";    
                                    }
                */
                                    //echo $sT->firstChild->firstChild->nodeName ."<br>";
                                // echo $sT->firstChild->firstChild->getAttribute("value") . "<br><br>";
                            }

                            else
                            {
                                echo "*** if condition failed! SimpleType Name and datatype name type not matching  ***";
                            }

                                
                        }
                                
                    }

                    function createTables($dX)
                    {
                        $elementDefs = $dX->evaluate("/xs:schema/xs:element");

                        foreach($elementDefs as $elementDef)
                        {
                            echo "<span id='keyword'>CREATE TABLE</span> " . $elementDef->getAttribute('name') . "(<br>";
                            defineAttribute($dX, $elementDef); //chnage
                        }    
                        echo ");";
                    }

                    function mapToSQL($schemaFilename)
                    {
                        createDatabase($schemaFilename);    
                        $domxpath = parseSchemaFile($schemaFilename);
                        createTables($domxpath);
                        //applyConstraints($domx
                    }


                    foreach($schemaFilenames as $sF)
                    {
                        $fileExtension = substr($sF, strpos($sF, ".")+1);

                        if($fileExtension === "xsd")
                        {
                        echo "<br><br>";
                        mapToSQL($sF);
                        echo "<br><br>";
                        }
                    }

                    echo "</div>";

                ?>

            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
    
    <?php include 'footer.php' ?>