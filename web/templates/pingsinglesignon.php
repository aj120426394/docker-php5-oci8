<?php
/**
 * Functions which will emulate the behaviour of SSO functions on server, without actually
 * performing the logging. This will allow for code to work in this VM without being modified
 * specifically for it.
 */

function getCurrentURL()
{
    $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $currentURL .= $_SERVER["SERVER_NAME"];

    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $currentURL .= ":".$_SERVER["SERVER_PORT"];
    }
    $currentURL .= $_SERVER["REQUEST_URI"];

    return $currentURL;
}

function singleSignOn()
{
  if (!isset($_GET['REF']) && !isset($_COOKIE['setSSO'])) {
    return array(false, -1);
  }

  if (isset($_GET['REF'])) {

    $groupMemberships = array();
    $affiliations = array();
      $groupMemberships[] = "cn=General Staff (All),ou=Groups,o=Griffith University";
      $groupMemberships[] = "cn=Staff (NA),ou=Groups,o=Griffith University";


      $affiliations[] = "EMPLOYEE";
      $affiliations[] = "GENERAL";

    return array(true, array(
      "userid" => "s123456",
      "name" => "Jane Doe",
      "emplid" => "s123456",
      "email" => "", // TODO: Currently returned as empty.
      "roles" => array(), // TODO: Verify this is actually defined in this method.
      "raw" => array(
        "mail" => "jane.doe@example.com",
        "partnerEntityID" => "priLoginForm",
        "sn" => "Doe",
        "com.pingidentity.plugin.instanceid" => "agentlessRID",
        "cn" => "s123456",
        "subject" => "s123456",
        "instanceId" => "agentlessRID",
        "guAffiliation" => $affiliations,
        "groupMembership" => $groupMemberships,
        "givenName" => "Jane",
        "sessionid" => $_GET['REF'],
        "authnInst" => (new DateTime())->format("o-m-d h:i:sO") // e.g. 2016-07-25 12:31:42+1000
      )
    ));
  } else {
    $redirectTo = getenv('BASE_URL') . "/login.php?TargetResource=" . urlencode(getCurrentURL());

    header("Location: " . $redirectTo, true, 302);
    exit;
  }
}

function singleSignonRedirect($version=1, $returnTo="", $appName="")
{
  $redirectTo = getenv('BASE_URL') . "/login.php?TargetResource=" . urlencode($returnTo);

  header("Location: " . $redirectTo, true, 302);
  exit;
}

function getPSLogoutByEnv()
{
  return getenv('BASE_URL') . "/logout.php";
}
