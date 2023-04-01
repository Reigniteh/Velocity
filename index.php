
<?php

// Import Velocity.
include_once($_SERVER['DOCUMENT_ROOT'].'/Velocity/autoload.php');

// Create a new VelocityTemplate instance.
$template = new VelocityTemplate();

// Create a new VelocitySource instance and pass it a file name.
$source = new VelocitySource("page.html");

// Set some variables and arrays
$template->set('title', 'My Page');
$template->set('year', date('Y'));
$template->set('link1', 'Link one');
$template->set('numbers', ['One', 'Two', 'Three', 'Four']);
$template->set('header', "10 links");
$template->set('burger', ['meat' => 'chicken',
                          'cheese' => 'cheddar',
                          'salad' => 'yes',
                          'Extra onion']);

// Render the template with the source, returns the rendered string.
$rendered = $template->render($source);

// Optionally you can render a string instead of a VelocitySource by passing a string in render() like this:
//
// $template = new VelocityTemplate();
// $template->set('title', 'My Page');
//
// $text = '<p>Here is some text that has a Velocity variable in it: $title</p>';
// $rendered = $template->render($text);
//
// echo $rendered;
// => <p>Here is some text that has a Velocity variable in it: My Page</p>


// Output the rendered HTML.
echo $rendered;

?>