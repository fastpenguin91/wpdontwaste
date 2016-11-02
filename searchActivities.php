<?php

/******

This admin page has two forms. Each form has their own callback that is created via their hidden field types. 
Each form generates all the "activity categories" and allows users to select which categories to include in their search.
The search is handled via the "DataObject class" and the form display is handled in FormDisplay.php

******/
include JSC_DONT_WASTE . '/models/FormDisplay.php';
$formObj = new Jsc_dont_waste\FormDisplay();

echo $formObj->all_conditions_heading();
echo $formObj->generate_conditions_form('all');
?>

<br>
<br>
<br>
<br>
<br>


<?php 
echo $formObj->any_conditions_heading();
echo $formObj->generate_conditions_form('any');
?>