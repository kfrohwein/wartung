<?php

  $searchString = "NOT Subject:Erfolgreich AND NOT Subject:Success";

  $finalArray = [];
  // Look for OR
  $termsOR = explode(" OR ",$searchString);

  // toJson the result and safe in database to remove this step?

  $termsAND = [];

  // Split the OR - Arrays by AND
  foreach ($termsOR as $term) {
      $tmpArray = [];
      foreach (explode(" AND ", $term) as $AND) {
        $tmp = explode(":", $AND);
        if(strpos($tmp[0], "NOT") !== false){
          echo "NOT";
        }

        $tmpKey = explode(" ", $tmp[0])[1];
        $tmpPre = explode(" ", $tmp[0])[0];
        array_push($tmpArray, [$tmpKey, $tmp[1], $tmpPre]);
      }
      array_push($finalArray, $tmpArray);
  }

print_r(JSON_encode($finalArray));
