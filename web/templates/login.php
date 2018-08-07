<?php
echo "SSO Login.";

$ref = md5(date('Y-m-d H:i:s'));

$targetResource = $_GET["TargetResource"];
$parts = preg_split("/\?/", $targetResource);

if (count($parts) > 1) {
  $targetResource = $parts[0];
}
?>

<form method="get" action="<?php echo $targetResource; ?>" onSubmit="document.cookie='setSSO=; path=/; domain=localhost;'">
  <input type="hidden" name="REF" value="<?php echo $ref; ?>"/>

  <?php
  if (count($parts) > 1) {
    $pairs = preg_split("/=/", $parts[1]);

    if (count($pairs) > 1) {
      echo "<input type=\"hidden\" name=\"" . $pairs[0] . "\" value=\"" . $pairs[1] . "\"/>";
    }
  }
  ?>

  <button type="submit">Login</button>
</form>
