<?php
require ROOT . "/vendor/autoload.php";
require ROOT . "/functions/display-errors.php";
require ROOT . "/functions/saveHook.php";
require ROOT . "/logs/logs.php";
require ROOT . "/functions/connectToCrm.php";
require ROOT . "/functions/refreshToken.php";
require ROOT . "/functions/crmMethods.php";

const
CRM_ENTITY_LEAD = "lead",
CRM_ENTITY_CONTACT = "contact",
CRM_ENTITY_COMPANY = "company",
CRM_PHONE_FIELD_ID = 1119405,
CRM_TAG_ID = 142093,
CRM_PIPELINE_ID = 6363914,
CRM_STATUS_ID = 54548406,
CRM_TASK_TYPE_ID = 2776594,
CRM_BOSS_ID = 8698579,



METHOD_GET = "GET",
METHOD_POST = "POST",
METHOD_PATCH = "PATCH";

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();