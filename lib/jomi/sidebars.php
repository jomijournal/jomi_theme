<?php

/*
=================================
CUSTOM SIDEBARS
=================================
*/

register_sidebar(array(
	'name' => __('About Sidebar'),
	'id' => 'sidebar-about',
	'description' => __('Sidebar for the About Page'),
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
) );

register_sidebar(array(
  'name' => __('Article Sidebar'),
  'id' => 'sidebar-article',
  'description' => __('Sidebar for Article Pages'),
  'before_widget' => '',
  'after_widget' => '',
  'before_title' => '<h3>',
  'after_title' => '</h3>',
) );

?>