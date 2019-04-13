<?php
$accept_lang="0";
$default_lang="es_ES";


function getLocaleList() {
    return array(
            "de_DE" => ["de_DE","de","de_DE.UTF-8","ger","german","german.1252"],
            "de"    => ["de_DE","de","de_DE.UTF-8","ger","german","german.1252"],
            "de-DE" => ["de_DE","de","de_DE.UTF-8","ger","german","german.1252"],
            "en_US" => ["en_us","en","en_US.UTF-8","eng","english","english.1252"],
            "en"    => ["en_us","en","en_US.UTF-8","eng","english","english.1252"],
            "en-US" => ["en_us","en","en_US.UTF-8","eng","english","english.1252"],
            "es_ES" => ["es_ES","es","es_ES.UTF-8","esp","spanish","spanish.1252"],
            "es"    => ["es_ES","es","es_ES.UTF-8","esp","spanish","spanish.1252"],
            "es-ES" => ["es_ES","es","es_ES.UTF-8","esp","spanish","spanish.1252"],
            "hu_HU" => ["hu_HU","hu","hu_HU.UTF-8","hun","hungarian","hungarian.1252"],
            "hu"    => ["hu_HU","hu","hu_HU.UTF-8","hun","hungarian","hungarian.1252"],
            "hu-HU" => ["hu_HU","hu","hu_HU.UTF-8","hun","hungarian","hungarian.1252"],
            "pt_PT" => ["pt_PT","pt","pt_PT.UTF-8","prt","portuguese","portuguese.1252"],
            "pt"    => ["pt_PT","pt","pt_PT.UTF-8","prt","portuguese","portuguese.1252"],
            "pt-PT" => ["pt_PT","pt","pt_PT.UTF-8","prt","portuguese","portuguese.1252"]
        );
}

function getPreferredLanguage($default,$accept_lang) {
    if (intval($accept_lang)===0) return $default;

    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return $default;
    $al=$_SERVER['HTTP_ACCEPT_LANGUAGE'];

    // regex inspired from @GabrielAnderson on http://stackoverflow.com/questions/6038236/http-accept-language
    preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $al, $lang_parse);
    $langs = $lang_parse[1];
    $ranks = $lang_parse[4];

    // (create an associative array 'language' => 'preference')
    $lang2pref = array();
    for($i=0; $i<count($langs); $i++)
        $lang2pref[$langs[$i]] = (float) (!empty($ranks[$i]) ? $ranks[$i] : 1);

    // (comparison function for uksort)
    $cmpLangs = function ($a, $b) use ($lang2pref) {
        if ($lang2pref[$a] > $lang2pref[$b])		return -1;
        elseif ($lang2pref[$a] < $lang2pref[$b])	return 1;
        elseif (strlen($a) > strlen($b))			return -1;
        elseif (strlen($a) < strlen($b))			return 1;
        else return 0;
    };

    // sort the languages by prefered language and by the most specific region
    uksort($lang2pref, $cmpLangs);

    // return the first value's key
    reset($lang2pref);
    return key($lang2pref);
}

function test($dl,$al) {
    $currentLocale = setlocale(LC_ALL, 0);
    echo "System locale is {$currentLocale}\n"; //outputs C/en_US.UTF-8/C/C/C/C on my machine


    $windows=(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')?true:false;

    // check for navigator preferences; on fail use default from config
    $locale = getPreferredLanguage($dl,$al);

    // get available locales on AgilityContest
    $locale_list = getLocaleList();
    if (!array_key_exists($locale, $locale_list)) $locale = $dl;

    // choose locale from availables based on config preferences
    $locales = $locale_list[$locale];
    echo "set locale between these availables: ".json_encode($locales)."\n";
    $sel = setlocale(LC_ALL, $locales);
    echo "selected locale is {$sel}\n";
    putenv("LC_ALL={$sel}");
    putenv("LANG={$sel}");
    putenv("LANGUAGE={$sel}");
    setlocale(LC_NUMERIC, ($windows) ? 'english' : 'en_US'); // Fix for float number with incorrect decimal separator.

    $currentLocale = setlocale(LC_ALL, 0);
    echo "New locale is {$currentLocale}\n"; //outputs C/en_US.UTF-8/C/C/C/C on my machine
}

test($default_lang,$accept_lang);