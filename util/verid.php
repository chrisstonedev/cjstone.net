<?php
$filePath = "exp/mobile-debug.apk";

$versionCode = getVersionCodeFromAPK($filePath);
echo $versionCode;


function getVersionCodeFromAPK($filePath) {
    $versionCode = 0;

    //AXML LEW 32-bit word (hex) for a start tag
    $XMLStartTag = "00100102";

    //APK is essentially a zip file, so open it
    $zip = zip_open($filePath);
    if ($zip) {
        while ($zip_entry = zip_read($zip)) {
            //Look for the AndroidManifest.xml file in the APK root directory
            if (zip_entry_name($zip_entry) == "AndroidManifest.xml") {
                //Get the contents of the file in hex format
                $axml = getHex($zip, $zip_entry);
                //Convert AXML hex file into an array of 32-bit words
                $axmlArr = convert2wordArray($axml);
                //Convert AXML 32-bit word array into Little Endian format 32-bit word array
                $axmlArr = convert2LEWwordArray($axmlArr);
                //Get first AXML open tag word index
                $firstStartTagword = findWord($axmlArr, $XMLStartTag);
                //The version code is 13 words after the first open tag word
                $versionCode = intval($axmlArr[$firstStartTagword + 13], 16);

                break;
            }
        }
    }
    zip_close($zip);

    return $versionCode;
}

//Get the contents of the file in hex format
function getHex($zip, $zip_entry) {
    if (zip_entry_open($zip, $zip_entry, 'r')) {
        $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        $hex = unpack("H*", $buf);
        return current($hex);
    }
}

//Given a hex byte stream, return an array of words
function convert2wordArray($hex) {
    $wordArr = array();
    $numwords = strlen($hex)/8;

    for ($i = 0; $i < $numwords; $i++)
        $wordArr[] = substr($hex, $i * 8, 8);

    return $wordArr;
}

//Given an array of words, convert them to Little Endian format (LSB first)
function convert2LEWWordArray($wordArr) {
    $LEWArr = array();

    foreach($wordArr as $word) {
        $LEWword = "";
        for ($i = 0; $i < strlen($word)/2; $i++)
            $LEWword .= substr($word, (strlen($word) - ($i*2) - 2), 2);
        $LEWArr[] = $LEWword;
    }

    return $LEWArr;
}

//Find a word in the word array and return its index value
function findWord($wordArr, $wordToFind) {
    $currentWord = 0;
    foreach ($wordArr as $word) {
        if ($word == $wordToFind)
            return $currentWord;
        else
            $currentWord++;
    }
}
?>