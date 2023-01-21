<?php
function csvCombiner($argv) {
    $argCount = count($argv);
    
    if($argCount < 3) {
        throw new Exception("Two or more filenames required!"); 
    }
    
    $excessArg = array_shift($argv); //Remove the first argument as this is just this file.
    $fileGroup = $argv; //Assign remaining arguments to clearer variable name.
    
    $allCategories = getCategories($fileGroup);
    array_push($allCategories, "filename");
    
    echo implode(",", $allCategories)."\r";
    
    foreach($fileGroup as $file) {
        $readHere = fopen($file, "r");
        
        //Read first line and get categories that exist in csv
        $fileCategoriesStr = str_replace(array("\r", "\n"), '', fgets($readHere));
        $fileCategoriesArr = explode (",", $fileCategoriesStr);
        
        while(!feof($readHere)) {
            $lineStr = fgets($readHere);//Get read string
            $lineStr = str_replace(array("\r", "\n"), '', $lineStr);
            
            if(!empty($lineStr)) {
                $strArr = explode (",", $lineStr);
                $string = categoryCompatibility($allCategories, $fileCategoriesArr, $strArr);
                $string = $string.basename($file)."\r";
            }

            echo $string;
        }
        fclose($readHere);
        unset($readHere);
    }
}

function getCategories($fileGroup) {
    $categoryArray = array();
    foreach($fileGroup as $file) {
        $readHere = fopen($file, "r");
        
        //read first line for categories
        $string = fgets($readHere);
        $string = str_replace(array("\r", "\n"), '', $string);
        
        $str_arr = explode (",", $string);
        
        foreach($str_arr as $element) {
            if (!in_array($element, $categoryArray)) {
                echo $element;
                array_push($categoryArray, $element);
            }
        }
    }
    
    return $categoryArray;
}

function categoryCompatibility($combinedCategories, $singleFileCategories, $strArray) {
    $returnStr = "";
    $numCategories = count($combinedCategories);
    $modifiedArr = array();
    
    for ($i = 0; $i < $numCategories - 1; $i++) {
        array_push($modifiedArr, "");
    }
    //print_r($modifiedArr);
    
    $i = 0;
    foreach($singleFileCategories as $category) {
        $offset = array_search($category, $combinedCategories);
       
        $modifiedArr[$offset] = $strArray[$i];

        $i = $i + 1; //iterator
    }
    
    $i = 0;
    
    
    while ($i < ($numCategories - 1)) {
        if ($modifiedArr[$i]) {
            $appendStr = $modifiedArr[$i];
            $returnStr = $returnStr.$appendStr;
        }
        $returnStr = $returnStr.',';
        $i = $i + 1;
    }
    
    return $returnStr;
}

csvCombiner($argv);
?>