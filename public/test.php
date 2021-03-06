<?php

function decode_windows1251($str){
    return mb_convert_encoding($str,"windows-1251",  "utf-8");
}

function iterateFiles($folder,$fn){
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder));


    foreach ($rii as $file) {

        if ($file->isDir()){
            continue;
        }

        $fn($file);

    }
}

//iterateFiles('my3/',function(SplFileInfo $file){
//    rename($file->getPathname(),$file->getPath() . '/'. decode_windows1251($file->getFilename()));
//});

$codes = [
    0 => 'pass',
    1 => 'auto',
    2 => 'wchar',
    3 => 'byte2be',
    4 => 'byte2le',
    5 => 'byte4be',
    6 => 'byte4le',
    7 => 'BASE64',
    8 => 'UUENCODE',
    9 => 'HTML-ENTITIES',
    10 => 'Quoted-Printable',
    11 => '7bit',
    12 => '8bit',
    13 => 'UCS-4',
    14 => 'UCS-4BE',
    15 => 'UCS-4LE',
    16 => 'UCS-2',
    17 => 'UCS-2BE',
    18 => 'UCS-2LE',
    19 => 'UTF-32',
    20 => 'UTF-32BE',
    21 => 'UTF-32LE',
    22 => 'UTF-16',
    23 => 'UTF-16BE',
    24 => 'UTF-16LE',
    25 => 'UTF-8',
    26 => 'UTF-7',
    27 => 'UTF7-IMAP',
    28 => 'ASCII',
    29 => 'EUC-JP',
    30 => 'SJIS',
    31 => 'eucJP-win',
    32 => 'EUC-JP-2004',
    33 => 'SJIS-win',
    34 => 'SJIS-Mobile#DOCOMO',
    35 => 'SJIS-Mobile#KDDI',
    36 => 'SJIS-Mobile#SOFTBANK',
    37 => 'SJIS-mac',
    38 => 'SJIS-2004',
    39 => 'UTF-8-Mobile#DOCOMO',
    40 => 'UTF-8-Mobile#KDDI-A',
    41 => 'UTF-8-Mobile#KDDI-B',
    42 => 'UTF-8-Mobile#SOFTBANK',
    43 => 'CP932',
    44 => 'CP51932',
    45 => 'JIS',
    46 => 'ISO-2022-JP',
    47 => 'ISO-2022-JP-MS',
    48 => 'GB18030',
    49 => 'Windows-1252',
    50 => 'Windows-1254',
    51 => 'ISO-8859-1',
    52 => 'ISO-8859-2',
    53 => 'ISO-8859-3',
    54 => 'ISO-8859-4',
    55 => 'ISO-8859-5',
    56 => 'ISO-8859-6',
    57 => 'ISO-8859-7',
    58 => 'ISO-8859-8',
    59 => 'ISO-8859-9',
    60 => 'ISO-8859-10',
    61 => 'ISO-8859-13',
    62 => 'ISO-8859-14',
    63 => 'ISO-8859-15',
    64 => 'ISO-8859-16',
    65 => 'EUC-CN',
    66 => 'CP936',
    67 => 'HZ',
    68 => 'EUC-TW',
    69 => 'BIG-5',
    70 => 'CP950',
    71 => 'EUC-KR',
    72 => 'UHC',
    73 => 'ISO-2022-KR',
    74 => 'Windows-1251',
    75 => 'CP866',
    76 => 'KOI8-R',
    77 => 'KOI8-U',
    78 => 'ArmSCII-8',
    79 => 'CP850',
    80 => 'JIS-ms',
    81 => 'ISO-2022-JP-2004',
    82 => 'ISO-2022-JP-MOBILE#KDDI',
    83 => 'CP50220',
    84 => 'CP50220raw',
    85 => 'CP50221',
    86 => 'CP50222',
];

$translates = [];
foreach ($codes as $code_1) {
    $translates[$code_1] = [];
    foreach ($codes as $code__2) {
        $translates[$code_1][$code__2] = mb_convert_encoding('????????????T????-T??-????T????-??-????T????????-_1-540x360',$code_1,$code__2) . "\r\n";
    }
}
echo '<pre>';
print_r($translates);
echo '</pre>';