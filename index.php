<?php
const ROOT = __DIR__;

require ROOT . "/functions/require.php";


logs();


// вебхук тригериться на создание контакта

if (file_exists("Tokens.json")) {


    if (!empty($_REQUEST["contacts"]["add"])) {

        if (!empty($_REQUEST["contacts"]["add"][0]["type"])) {

            if ($_REQUEST["contacts"]["add"][0]["type"] === "contact") {

                saveHook($_REQUEST);


            }


        }

    }


}














