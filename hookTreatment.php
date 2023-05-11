<?php

const ROOT = __DIR__;

require ROOT . "/functions/require.php";





$hooksFolder = scandir(ROOT."/hooks");

$hooksSortedFolder = [];

foreach ($hooksFolder as $value) {

    if(substr_count($value, ".json")) {

        $hooksSortedFolder[] = $value;

    }

}

$hooksArrayNames = [];

switch (true) {

    case count($hooksSortedFolder) === 0:
        die;

    case count($hooksSortedFolder) <= 10:


        foreach ($hooksSortedFolder as $value) {

            if(substr_count($value, ".json")) {
                $hooksArrayNames[] = $value;

            }
        }

        break;

    case count($hooksSortedFolder) > 10:

        for($i = 0; $i < 10; $i++) {

            if(substr_count($hooksSortedFolder[$i], ".json")) {
                $hooksArrayNames[] = $hooksSortedFolder[$i];

            }

        }


}




foreach ($hooksArrayNames as $value) {



    $data = file_get_contents(ROOT . "/hooks/$value");
    $data = json_decode($data, true);

//    айди и дата создания контакта с хука
    $id = (int)$data[0]["contacts"]["add"][0]["id"];

    $responsible_user_id = (int)$data[0]["contacts"]["add"][0]["responsible_user_id"];
    $usersIds = [];
    // айди текущего пользователя

//    $currentUser = (int)getAccount()["current_user_id"];
    $usersIds[] = $responsible_user_id;

// айди руководителя

    $usersIds[] = CRM_BOSS_ID;



// получает контакт по id, там есть поле телефона
    $ContactIdSearch = getEntity(CRM_ENTITY_CONTACT, $id);
    echo "<pre>";



    foreach ($ContactIdSearch["custom_fields_values"] as $fieldValue) {


//        проверка на телефон у контакта
        if ($fieldValue["field_id"] == CRM_PHONE_FIELD_ID) {

            $phone = $fieldValue["values"][0]["value"];

//        по телефону ищет контакт/контакты

            $contactsWithPhone = searchEntity(CRM_ENTITY_CONTACT, $phone);


//          заготовку для тега
            $tagData = [
                "id" => $id,
                "_embedded" => [
                    "tags" => [

                        [
                            "id" => CRM_TAG_ID
                        ]

                    ]
                ]
            ];


// уже найденные контакты с номером телефона, который был в хуке
            if (count($contactsWithPhone["_embedded"]["contacts"]) > 1) {

                $contacts = $contactsWithPhone["_embedded"]["contacts"];

                $earlyContactTime = 0;

                foreach ($contacts as $contact) {

                    if ($earlyContactTime < $contact["created_at"]) {

                        $earlyContactTime = $contact["created_at"];

                    }

                }


                foreach ($contacts as $contact) {


                    //   $id $responsible_user_id $created_at

//                  ищет соответствие в пришедших контактах с тем, что в хук проверяется


                    if ($contact["id"] == $id && $contact["responsible_user_id"] == $responsible_user_id && $contact["created_at"] == $earlyContactTime) {


                        if (!empty($contact["_embedded"]["tags"])) {

                            $tags = $contact["_embedded"]["tags"];
//
                            $deleteTagStatus = null;

                            foreach ($tags as $tag) {
//                        проверка на уже установленный тег
                                if ($tag["id"] == 142093) {


                                    $deleteTagStatus = true;

                                }
                            }

//
                            if (!isset($deleteTagStatus)) {

                                addTask($contact["id"], $usersIds);
                                entityChanges(CRM_ENTITY_CONTACT, $tagData);


                            }




                        } else {
                            addTask($contact["id"], $usersIds);
                            entityChanges(CRM_ENTITY_CONTACT, $tagData);
                        }


                        if (!empty($contact["_embedded"]["leads"])) {


                            foreach ($contact["_embedded"]["leads"] as $lead) {

                                $leadInfo = getEntity(CRM_ENTITY_LEAD, $lead["id"]);

                                $nowDay = date("Y-m-d");

                                $leadDay = date("Y-m-d", (int)$leadInfo["created_at"]);
//                          если сделка создана в этот же день с дубликатом контакта, то она перенесется в воронку дублей
                                if ($nowDay == $leadDay) {


                                    $pipelineData = [

                                        "id" => (int)$lead["id"],
                                        "pipeline_id" => CRM_PIPELINE_ID,
                                        "status_id" => CRM_STATUS_ID

                                    ];


                                    entityChanges(CRM_ENTITY_LEAD, $pipelineData);

                                }


                            }

                        }


                        }  else if ($contact["id"] == $id && $contact["responsible_user_id"] == $responsible_user_id) {

                        addTask($contact["id"], $contact["responsible_user_id"]);

                    }


                }
            }


        }

    }

}





foreach ($hooksArrayNames as $value) {

    unlink(ROOT."/hooks/$value");



}













