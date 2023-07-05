<?php
include 'array.php';

function getPartsFromFullname($fullname) {
    $parts = explode(' ', $fullname);
    return [
        'surname' => $parts[0],
        'name' => $parts[1],
        'patronomyc' => $parts[2]
    ];
}

function getFullnameFromParts($surname, $name, $patronomyc) {
    return $surname . ' ' . $name . ' ' . $patronomyc;
}

function getShortName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $surname = $parts['surname'];
    $name = $parts['name'];
    $patronomyc = $parts['patronomyc'];

    $shortSurname = mb_substr($surname, 0, 1, 'UTF-8'); 
    $shortName = $name . ' ' . $shortSurname . '.'; 

    return $shortName;
}
function getGenderFromName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $surname = $parts['surname'];
    $name = $parts['name'];
    $patronomyc = $parts['patronomyc'];
  
    $genderScore = 0;
  
    // Проверка женских  индикаторов
    if (mb_substr($patronomyc, -3, 3, 'UTF-8') === 'вна') {
        $genderScore--;
    }
    if (mb_substr($name, -1, 1, 'UTF-8') === 'а') {
        $genderScore--;
    }
    if (mb_substr($surname, -2, 2, 'UTF-8') === 'ва') {
        $genderScore--;
    }
  
    // Проверка мужских  индикаторов
    if (mb_substr($patronomyc, -3, 3, 'UTF-8') === 'ич') {
        $genderScore++;
    }
    if (mb_substr($name, -1, 1, 'UTF-8') === 'й' || mb_substr($name, -1, 1, 'UTF-8') === 'н' || mb_substr($name, -1, 1, 'UTF-8') === 'р') {
        $genderScore++;
    }
    if (mb_substr($surname, -1, 1, 'UTF-8') === 'в') {
        $genderScore++;
    }
  
    if ($genderScore > 0) {
        return 1; // Мужской пол
    } elseif ($genderScore < 0) {
        return -1; // Женский пол
    } else {
        return 0; // Пол неопределен
    }
}

function getGenderDescription($example_persons_array) {
    $totalPersons = count($example_persons_array);
    $maleCount = 0;
    $femaleCount = 0;
    $undefinedCount = 0;
  
    foreach ($example_persons_array as $person) {
        $fullname = $person['fullname'];
        $gender = getGenderFromName($fullname);
      
        if ($gender === 1) {
            $maleCount++;
        } elseif ($gender === -1) {
            $femaleCount++;
        } else {
            $undefinedCount++;
        }
    }
  
    $malePercentage = round(($maleCount / $totalPersons) * 100, 1);
    $femalePercentage = round(($femaleCount / $totalPersons) * 100, 1);
    $undefinedPercentage = round(($undefinedCount / $totalPersons) * 100, 1);
  
    $genderDescription = "Гендерный состав аудитории:<br>";
    $genderDescription .= "---------------------------<br>";
    $genderDescription .= "Мужчины - $malePercentage%<br>";
    $genderDescription .= "Женщины - $femalePercentage%<br>";
    $genderDescription .= "Не удалось определить - $undefinedPercentage%<br>";
  
    return $genderDescription;
}

function getPerfectPartner($surname, $name, $patronomyc, $example_persons_array) {
    
    $surname = ucwords(strtolower($surname));
    $name = ucwords(strtolower($name));
    $patronomyc = ucwords(strtolower($patronomyc));

    $fullname = getFullnameFromParts($surname, $name, $patronomyc);
    $gender = getGenderFromName($fullname);

    include 'array.php';    

    do {
        $randomIndex = array_rand($example_persons_array);
        $partnerFullname = $example_persons_array[$randomIndex]['fullname'];
        $partnerGender = getGenderFromName($partnerFullname);
    } while ($gender === $partnerGender || $partnerGender === 0);

    $compatibilityPercentage = mt_rand(50, 100) / 100; 

    $perfectPair = "$fullname + $partnerFullname = \n";
    $perfectPair .= "♡ Идеально на " . number_format($compatibilityPercentage * 100, 2) . "% ♡";

    return $perfectPair;
}

// Код для проверки и вывода на экран

$randomPersonIndex = array_rand($example_persons_array);
$randomPerson = $example_persons_array[$randomPersonIndex];
$fullname = $randomPerson['fullname'];

$parts = getPartsFromFullname($fullname);
$surname = $parts['surname'];
$name = $parts['name'];
$patronomyc = $parts['patronomyc'];

echo "Случайное имя из списка: $fullname<br>";
echo "Surname: $surname<br>";
echo "Name: $name<br>";
echo "Patronomyc: $patronomyc<br>";

$gender = getGenderFromName($fullname);
if ($gender === 1) {
    echo "Пол: Мужской<br>";
} elseif ($gender === -1) {
    echo "Пол: Женский<br>";
} else {
    echo "Пол: Неопреледен<br>";
}


$shortName = getShortName($fullname);
echo "Short Name: $shortName<br>";

$genderDescription = getGenderDescription($example_persons_array);
echo $genderDescription;

$perfectPartner = getPerfectPartner($surname, $name, $patronomyc);
echo $perfectPartner;

