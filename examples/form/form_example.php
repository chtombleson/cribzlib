<?php
require_once(dirname(__FILE__).'/../../cribzlib.php');
$cribzlib = new CribzLib();

// Load Form module
$cribzlib->loadModule('Form');

// Create a new instance of the form class.
// Parameter 1 is the action url.
// Parameter 2 is do you want to use post for your requests.
$form = new CribzForm('form_example.php', true);

// Add a textbox to the form
// Parameter 1 is the type of form input.
// Parameter 2 is the name for the input field, this is used by $_POST and $_GET.
// Parameter 3 is this field required of not.
// Parameter 4 is the label(Optional).
$form->addElement('text', 'firstname', true, 'First Name &nbsp');

// Add a dropdown to the form
// Parameter 1 is the type of form input.
// Parameter 2 is the name for the input field, this is used by $_POST and $_GET.
// Parameter 3 is this field required of not.
// Parameter 4 is the label(Optional).
// Parameter 5 is the max length of input(Optional).
// Parameter 6 is the min length of input(Optional).
// Parameter 7 is an Optional class.
// Parameter 8 is a regex expression to match the input against(Optional).
// Parameter 9 is an array of options, normally this is Optional but is required for a select(Dropdown) input.
$form->addElement('select', 'gender', true, 'Gender &nbsp', null, null, '', '/.*/', array('female' => 'female', 'male' => 'male'));

// Display the form
echo $form->render();

// Handle the form
if (isset($_POST['submit'])) {
    unset($_POST['submit']);

    // Validate the input from the form
    $valid = $form->validate($_POST);

    if ($valid === true) {
        echo "<br />";
        echo "Name: ".$_POST['firstname']."<br />";
        echo "Gender: ".$_POST['gender']."<br />";
    } else {
        echo "Input not valid!!!!<br />";
    }
}
?>
