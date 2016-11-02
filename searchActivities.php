<?php

/******

This entire page has two forms. Each form has their own callback that is created via their hidden field types. 
Each form generates all the "activity categories" and allows users to select which categories to include in their search.
The search is handled via the "DataObject class"

******/

echo "<h3>Meet ALL Conditions Search</h3>";
echo "<p>Check all the categories that an activity MUST be in. If you wanted a solo indoor physical activity you could ". 
"check <strong>'solo', 'indoor', and 'physical'</strong> to only get activities that are Solo, Indoor AND physical.</p>";

$args = array(
    'hide_empty' => true
);

$terms = get_terms('jsc_activity_cat', $args);
?>

<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="get">
    <input type="hidden" name="action" value="meet_all_conditions">
<?php

$htmlForm = '';

foreach( $terms as $theTerm ) {
    $vars = get_object_vars($theTerm);
    $htmlForm .= '<label><input type="checkbox" name="activities[]" id="'. $vars['term_id'] . '" value="'. $vars['term_id'] . '"> ' . $vars['name'] . '</label>'; 
}

echo $htmlForm;

?>

<input type="submit" value="get random activity!">
</form>
<br>
<br>
<br>
<br>
<br>

<h3>Meet ANY Criteria Search</h3>
<p>Check any category that you want to include. This search is an "EITHER OR" search. If you want something that's either X OR Y or Z or ..., then select all categories you want to include.</p>

<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="get">
    <input type="hidden" name="action" value="meet_any_conditions">
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