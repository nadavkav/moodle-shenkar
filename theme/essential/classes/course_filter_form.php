<?php

/*  Filter courses by categories and roles form function
 *  Used in:
 *      theme_essential_core_user_renderer::courselist()
 *      theme_essential_core_course_renderer::frontpage_my_courses()
*/

function filter_courses_form() {
    global $DB, $CFG;

    $html = '';

    $filterbycategory = optional_param('filterByCategory', $CFG->defaultcoursecategroy, PARAM_INT);
    $filterbyrole = optional_param('filterByRole', -1, PARAM_INT);
    $filterbysemester = optional_param('filterBySemester', -1, PARAM_INT);

    $semesterlist = array('-1'=>get_string('all'));
    //$semesterlistkeys = explode(',', get_string('semesterlistkeys', 'theme_essential'));
    foreach (explode(',',get_string('semesterlist','theme_essential')) as $key => $semester) {
        //$semesterlist[$semesterlistkeys[$key]] = $semester;
        $semesterlist[] = $semester;
    }

    if (isset($CFG->showonlytopcategories) && $CFG->showonlytopcategories) {
        $showonlytopcategories = true;
    } else{
        $showonlytopcategories = false;
    }

    require_once($CFG->libdir . '/coursecatlib.php');

    $childrencats = array();
    $categories['-1'] =  get_string('showallcourses', 'theme_essential');    //Add all courses option (No filter)
    if ($showonlytopcategories) {  //Show only top categories

        foreach (coursecat::get(0)->get_children() as $category) {
            $categories[$category->id] = $category->name;
        }

        if ($filterbycategory > 0){ //If filter is set get a list of child categories
            foreach (coursecat::get($filterbycategory)->get_children() as $category) {
                $childrencats[$category->id] = $category->name;
            }
        }
    } else {  //Show all categories
        $fullcategories = coursecat::make_categories_list();
        //$categories = array_merge($categories, $fullcategories);
        $categories = $categories + $fullcategories;
    }

    $rolestudent = $DB->get_record('role', array('shortname'=>'student'));
    $roleteacher = $DB->get_record('role', array('shortname'=>'editingteacher'));

    $roles = array();
    $roles['-1'] =  get_string('anyrole', 'theme_essential');    //Add all courses option (No filter)
    $roles[$rolestudent->id] = $rolestudent->name;
    $roles[$roleteacher->id] = $roleteacher->name;

    $html .= html_writer::start_tag('form',array('id'=>'frmFilters', 'action'=>'', 'method'=>'post'));
    $html .= html_writer::start_tag('div',array('style'=>'width:95%;margin-left:auto;margin-right:auto;'));
    $html .= html_writer::start_tag('h4');

    $html .= get_string('filterby', 'theme_essential');
    //$html .= get_string('filterbycategory', 'theme_essential');
    $html .= html_writer::select($categories, 'filterByCategory', $filterbycategory, '',
        array(  'onchange' => 'this.form.submit()'));
    //,'style'=>'margin-left:auto;margin-right:auto;'));
    $html .= '&nbsp;&nbsp;';

    /*
            $html .= get_string('filterbyrole', 'theme_essential');
            $html .= html_writer::select($roles, 'filterByRole', $filterbyrole, get_string('choose'),
                array('onchange' => 'this.form.submit()'));
    */
    $html .= get_string('filterbysemester', 'theme_essential');
    $html .= html_writer::select($semesterlist, 'filterBySemester', $filterbysemester, '' /* get_string('choose') */,
        array('onchange' => 'this.form.submit()'));

    //$html .= html_writer::start_tag('h4');
    $html .= html_writer::end_tag('div');
    $html .= html_writer::end_tag('form');

    return array($categories, $childrencats, $roles, $filterbycategory, $filterbyrole, $filterbysemester, $html);

}