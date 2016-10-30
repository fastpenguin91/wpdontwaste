<?php
global $wpdb;

echo "<h3>AND Search</h3>";
echo "<p>Check all the categories that an activity MUST be in. If you were by yourself and wanted an indoor physical activity you could ". 
"check <strong>'solo', 'indoor', and 'physical'</strong> to get activities that match ALL the criteria. You'd only get activities that are Solo, Indoor AND physical.</p>";

$args = array(
    'hide_empty' => true
    );

$terms = get_terms('jsc_activity_cat', $args);


?>

<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="get">
    <input type="hidden" name="action" value="contact_form">
<?php

$htmlForm = '';

foreach( $terms as $theTerm ) {

    $vars = get_object_vars($theTerm);


    $htmlForm .= '<label><input type="checkbox" name="activities[]" id="'. $vars['term_id'] . '" value="'. $vars['term_id'] . '"> ' . $vars['name'] . '</label>'; 

}

echo $htmlForm;

?>

<input type="submit" value="get random activity!">
</form> <br>
<br>
<br>
<br>
<br>

<h3>OR Search</h3>
<p>Check any category that you want to include. This search is an "EITHER OR" search. If you want something that's either Indoor OR outdoor, then select all categories you want to include.</p>



<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="get">
    <input type="hidden" name="action" value="random_or">
<?php

$htmlForm2 = '';

foreach( $terms as $theTerm ) {

    $vars = get_object_vars($theTerm);


    $htmlForm2 .= '<label><input type="checkbox" name="activities[]" id="'. $vars['term_id'] . '" value="'. $vars['term_id'] . '"> ' . $vars['name'] . '</label>'; 

}

echo $htmlForm2;

?>

<input type="submit" value="get random activity!">
</form> <br>