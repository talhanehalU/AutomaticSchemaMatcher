<?php

session_start();

$_SESSION['schemaFilenames'] = $_FILES['sourceSchemas']['name'];

if(isset($_POST['upload']))
{
    $filesCount = count($_FILES['sourceSchemas']['name']);
   
    for($i = 0; $i < $filesCount; $i++)
    {
        $fileName = $_FILES['sourceSchemas']['name'][$i];

        $fileTmpName = $_FILES['sourceSchemas']['tmp_name'][$i];
        $fileSize = $_FILES['sourceSchemas']['size'][$i];
        $fileError = $_FILES['sourceSchemas']['error'][$i];
        $fileType = $_FILES['sourceSchemas']['type'][$i];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowedFileFormat = array('xsd', 'json', 'csv', 'sql', 'jpg');

        if(in_array($fileActualExt, $allowedFileFormat))
        {
            if($fileError === 0)
            {
                if($fileSize < 1000000)
                {
                    $fileDestination = 'sourceSchemas/' . $fileName;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    $status = "Schema Files Uploaded Successfully!";
                    $_SESSION['uploadStatus'] = $status;
                    header("Location: index.php?uploadSuccess");
                }

                else
                {
                    $status = "Error: Schema file you uploaded it is too big";
                    $_SESSION['uploadStatus'] = $status;
                    header("Location: uploadSchema.php?uploadError");
                }
            }

            else
            {
                $status = "Error: Error  occured during upload of schema file!";
                $_SESSION['uploadStatus'] = $status;
                header("Location: uploadSchema.php?uploadError");
            }
        }

        else
        {
            $status = "Error: Only XSD, JSON, CSV or SQL schemas are allowed!";
            $_SESSION['uploadStatus'] = $status;
            header("Location: uploadSchema.php?uploadError");
        }
    }

}  

?>