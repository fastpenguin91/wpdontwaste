<?php

namespace Jsc_dont_waste;

class FormDisplay {

    public function all_conditions_heading(){
        $headingText = "<h3>" . esc_html__('Meet ALL Conditions Search', 'jsc-dont-waste') . "</h3>";
        $headingText .= "<p>" . "Check all the categories that an activity MUST be in. If you wanted a solo indoor physical activity you could "; 
        $headingText .= "check <strong>'solo', 'indoor', and 'physical'</strong> to only get activities that are Solo, Indoor AND physical.</p>";
        return $headingText;
    }

    public function any_conditions_heading(){
        $headingText = "<h3>" . esc_html__('Meet ANY Conditions Search', 'jsc-dont-waste') . "</h3>";
        $headingText .= "<p>Check any category that you want to include. This search is an \"EITHER OR\" search.";
        $headingText .= "If you want something that's either X OR Y or Z or ..., then select all categories you want to include.</p>";
        return $headingText;
    }

    public function generate_conditions_form($formValue){

        if ($formValue == 'all') {
            $conditionsType = 'meet_all_conditions';
        } else if ($formValue == 'any') {
            $conditionsType = 'meet_any_conditions';
        }

        $args = array(
            'hide_empty' => true
        );

        $terms = get_terms('jsc_activity_cat', $args);

        $htmlForm = '<form action="' . esc_url( admin_url('admin-post.php') ) .'" method="get">
        <input type="hidden" name="action" value="' . $conditionsType . '">';

        foreach( $terms as $theTerm ) {
            $vars = get_object_vars($theTerm);
            $htmlForm .= '<label class="dontwaste_label"><input type="checkbox" name="activities[]" id="'. $vars['term_id'] . '" value="'. $vars['term_id'] . '"> ' . $vars['name'] . '</label>'; 
        }

        $htmlForm .= '<input type="submit" value="' . esc_html__('get random activity!', 'jsc-dont-waste') . '"></form>';
        return $htmlForm;
    }

}

?>