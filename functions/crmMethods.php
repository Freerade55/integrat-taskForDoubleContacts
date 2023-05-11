<?php


//  Выводит по id сущность, можно передать любую. Сделку, компанию и тд
function getEntity(string $entity_type, int $id): array
{
    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts/$id?with=leads";
            break;
        case CRM_ENTITY_LEAD:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads/$id?with=contacts";
            break;
        case CRM_ENTITY_COMPANY:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies/$id?with=contacts";
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }


}


//  Ищет сущность по строке, можно передать любую. Сделку, компанию и тд.
function searchEntity(string $entity_type, string $search): array
{


    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $query = [
                "with" => "leads",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts?" . http_build_query($query);
            break;
        case CRM_ENTITY_LEAD:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads?" . http_build_query($query);
            break;
        case CRM_ENTITY_COMPANY:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies?" . http_build_query($query);
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }

}












// Добавляет тег
function entityChanges(string $entity_type, array $data) {
    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts";
            break;
        case CRM_ENTITY_LEAD:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads";
            break;
        case CRM_ENTITY_COMPANY:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies";
            break;
    }

    $result = json_decode(connect($link, METHOD_PATCH, [$data]), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }
}






// Аккаунт
function getAccount(): array
{

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/account";


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }





}






// добавление задачи по дублю контакта
function addTask(int $contactId, $responsible_user_id)

{
    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/tasks";



    if (gettype($responsible_user_id) == "array") {



        foreach ($responsible_user_id as $value) {


            $timestamp = time() + 60*60;


            $queryData = array(
                [
                    "text" => "Создан дубль контакта",
                    "entity_id" => $contactId,
                    "complete_till" => $timestamp,
                    "entity_type" => "contacts",
                    "task_type_id" => CRM_TASK_TYPE_ID,
                    "responsible_user_id" => $value

                ]
            );

            json_decode(connect($link, METHOD_POST, $queryData), true);

        }

    } else {

        $timestamp = time() + 60*60;


        $queryData = array(
            [
                "text" => "Создан дубль контакта",
                "entity_id" => $contactId,
                "complete_till" => $timestamp,
                "entity_type" => "contacts",
                "task_type_id" => CRM_TASK_TYPE_ID,
                "responsible_user_id" => $responsible_user_id

            ]
        );


//
        json_decode(connect($link, METHOD_POST, $queryData), true);
    }







}


