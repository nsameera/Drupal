<?php

/**
 * @file
 * Creates a restaurant table with desired output.
 */

 /**
  * Implements restaurant_display function.
  */
function restaurant_display() {

  $query = db_select('restaurant_menu', 'ra');
  $query
    ->fields('ra', array('menu_id',
      'menu_name',
      'menu_description',
      'menu_type',
      'menu_price',
      'menu_img',
    )
  )
    ->condition('menu_type', 'appetizer')
    ->range(0, 50)
    ->orderby('ra . menu_id');
  $resultset = $query->execute();
  $header[] = t('ID');
  $header[] = t('Menu Name');
  $header[] = t('Menu Description');
  $header[] = t('Menu Type');
  $header[] = t('Menu Price');
  $header[] = t('Menu Images');
  $rows = array();
  foreach ($resultset as $result) {
    if ($result->menu_name == 'Pakora') {
      $rows[] = array(
        check_plain($result->menu_id),
        check_plain($result->menu_name),
        check_plain($result->menu_description),
        check_plain($result->menu_type),
        check_plain($result->menu_price),
        "<img src= '/sites/all/modules/images/pakora.jpeg'>",

      );
    }
    elseif ($result->menu_name == 'Samosa chat') {
      $rows[] = array(
        check_plain($result->menu_id),
        check_plain($result->menu_name),
        check_plain($result->menu_description),
        check_plain($result->menu_type),
        check_plain($result->menu_price),
        "<img src= '/sites/all/modules/images/samosa.jpeg'>",
      );
    }
  }
  return theme('table', array('header' => $header, 'rows' => $rows));
}

/**
 * Implements restaurant_main().
 */
function restaurant_main() {

  $query = db_select('restaurant_menu', 'ra');
  $query
    ->fields('ra', array('menu_id',
      'menu_name',
      'menu_description',
      'menu_type',
      'menu_price',
      'menu_img',
    )
  )
    ->condition('menu_type', 'maincourse', '=')
    ->range(0, 50)
    ->orderby('ra . menu_id');
  $resultset = $query->execute();

  $header[] = t('ID');
  $header[] = t('Menu Name');
  $header[] = t('Menu Description');
  $header[] = t('Menu Type');
  $header[] = t('Menu Price');
  $header[] = t('Menu Images');
  $rows = array();
  foreach ($resultset as $result) {
    if ($result->menu_name == 'Biryani') {
      $rows[] = array(
        check_plain($result->menu_id),
        check_plain($result->menu_name),
        check_plain($result->menu_description),
        check_plain($result->menu_type),
        check_plain($result->menu_price),
        "<img src= '/sites/all/modules/images/biryani.jpeg'>",
      );
    }
    elseif ($result->menu_name == 'Idli sambar') {
      $rows[] = array(
        check_plain($result->menu_id),
        check_plain($result->menu_name),
        check_plain($result->menu_description),
        check_plain($result->menu_type),
        check_plain($result->menu_price),
        "<img src= '/sites/all/modules/images/idli.jpeg'>",
      );
    }
  }
  return theme('table', array('header' => $header, 'rows' => $rows));
}

/**
 * Implements restaurant_dessert().
 */
function restaurant_dessert() {
  $query = db_select('restaurant_menu', 'ra');
  $query
    ->fields('ra', array('menu_id',
      'menu_name',
      'menu_description',
      'menu_type',
      'menu_price',
      'menu_img',
    )
  )
    ->condition('menu_type', 'dessert', '=')
    ->range(0, 50)
    ->orderby('ra . menu_id');
  $resultset = $query->execute();
  $header[] = t('ID');
  $header[] = t('Menu Name');
  $header[] = t('Menu Description');
  $header[] = t('Menu Type');
  $header[] = t('Menu Price');
  $header[] = t('Menu Images');
  $rows = array();
  foreach ($resultset as $result) {
    if ($result->menu_name == 'Gulab Jamun') {
      $rows[] = array(
        check_plain($result->menu_id),
        check_plain($result->menu_name),
        check_plain($result->menu_description),
        check_plain($result->menu_type),
        check_plain($result->menu_price),
      /*l($result->menu_name, $result->menu_img),*/
        "<img src= '/sites/all/modules/images/gulabjamun.jpeg'>",
      );
      /*return theme('table', array('header' => $header, 'rows' => $rows));*/
    }
    elseif ($result->menu_name == 'Ice Cream') {
      $rows[] = array(
        check_plain($result->menu_id),
        check_plain($result->menu_name),
        check_plain($result->menu_description),
        check_plain($result->menu_type),
        check_plain($result->menu_price),
        "<img src= '/sites/all/modules/images/icecream.jpeg'>",
      );
    }
  }
  return theme('table', array('header' => $header, 'rows' => $rows));
}
