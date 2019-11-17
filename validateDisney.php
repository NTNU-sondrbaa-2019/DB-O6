<?php
$debug = false;
$options = getopt("", array("debug"));
if (isset($options["debug"])) {
    print("*** Debug mode ***\n\n");
    $debug = true;
}

define("DISNEY_SCHEMA_URL", "Disney.xsd");

$VALID_DOCS = array("Disney.xml",
                    "DisneyMinimal.xml"
);

$INVALID_DOCS = array(
    array("file" => "DisneyNoSubsidiary.xml",
          "problem" => "There must be at least one Subsidiary."),
    array("file" => "DisneyInvalidDate.xml",
          "problem" => "Dates must be in the format YYYY-MM-DD."),
    array("file" => "DisneyInvalidPlace.xml",
          "problem" => "Locations must include both a place and a country, comma separated."),
    array("file" => "DisneyNoActorId.xml",
          "problem" => "Actors must have id attributes."),
    array("file" => "DisneyDuplicateActor.xml",
          "problem" => "An actor must only be listed once."),
    array("file" => "DisneyInvalidLanguage.xml",
          "problem" => "Language must be a two-letter code."),
    array("file" => "DisneyDuplicateSubsidiary.xml",
          "problem" => "A subsidiary must be listed only once."),
    array("file" => "DisneyNoSubsidiaryId.xml",
          "problem" => "Subsidiaries must have id attributes."),
    array("file" => "DisneyInvalidScreenTimeUnits.xml",
          "problem" => "Screen time units must be either minutes or seconds."),
    array("file" => "DisneyNoActorName.xml",
          "problem" => "Roles must have name attributes."),
    array("file" => "DisneyNoActorRef.xml",
          "problem" => "Roles must have actor attributes."),
    array("file" => "DisneyInvalidActorRef.xml",
          "problem" => "Actor attributes in a role must match an actor's id."),
    array("file" => "DisneyInvalidScreenTimeUnits.xml",
          "problem" => "Screen time units must be either minutes or seconds."),
    array("file" => "DisneyDuplicateMovie.xml",
          "problem" => "A movie must only be listed once.")
);


function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

// Enable user error handling
if (!$debug){
    libxml_use_internal_errors(true);
}

$doc = new DOMDocument();

foreach ($VALID_DOCS as $url) {
    $doc->load($url);
    if (!$doc->schemaValidate(DISNEY_SCHEMA_URL)) {
        print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
        libxml_display_errors();
    } else {
        print("$url is valid.\n");
    }
}

foreach ($INVALID_DOCS as $testDoc){
    $doc->load($testDoc["file"]);
    if (!$doc->schemaValidate(DISNEY_SCHEMA_URL)) {
        print("{$testDoc["file"]}: Validation failed as expected.\n");
    } else {
        print("ERROR: Validation succeeded but {$testDoc["file"]} is not valid: {$testDoc["problem"]}\n");
    }
}

?>
