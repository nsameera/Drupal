<?php
/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in the ../system directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * CUSTOMIZATIONS:
 * - $myappslink: provides a link for the users my apps page (with glyphicon)
 * - $profilelink: provides a link for the user profile page (with glyphicon)
 * - $logoutlink: provides a link for the user to log out (with glyphicon)
 * - $company_switcher: Provides the dropdown to switch companies if
 *   apigee_company module is enabled.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see bootstrap_preprocess_page()
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see bootstrap_process_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */

$can_register = (variable_get('user_register', USER_REGISTER_VISITORS_ADMINISTRATIVE_APPROVAL) != USER_REGISTER_ADMINISTRATORS_ONLY);
?>
<div class="top_header">
  <h4><img src="http://devportal-s.dd:8083/sites/all/themes/first_subtheme/favicon.ico" alt="New drupal logo">
  Developer Portal</h4>
</div>
<header id="navbar" role="banner" class="<?php print $navbar_classes; ?>">
    <div class="container">
       <div class="navbar-header">
            <?php if ($logo): ?>
                <a class="logo navbar-btn pull-left" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
                    <img src="<?php print $logo; ?>" alt="<?php print $site_name; ?>" />
                </a>
            <?php endif; ?>

            <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-collapse collapse">
            <nav role="navigation">
                <?php if (!empty($primary_nav)): print render($primary_nav); endif; ?>
                <ul class="menu nav navbar-nav pull-right account-menu">
                    <?php if (user_is_anonymous()): ?>
                        <?php if ($can_register): ?><li class="<?php echo (($current_path == 'user/register') ? 'active' : ''); ?>"><?php echo l(t('Register'), 'user/register'); ?></li><?php endif; ?>
                        <li class="<?php echo (($current_path == 'user/login') ? 'active' : ''); ?>"><?php echo l(t('Login'), 'user/login'); ?></li>
                    <?php else: ?>
                        <li class="first expanded dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle" data-target="#" title="" href="/user">
                                <?php print $user_mail_clipped; ?> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu"><?php print $dropdown_links; ?></ul>
                        </li>
                        <li class="last"><?php print l(t('Logout'), 'user/logout'); ?></li>
                    <?php endif; ?>
                </ul>
                <?php if (!empty($page['navigation'])): print render($page['navigation']); endif; ?>
            </nav>
        </div>
    </div>
</header>
<div class="bottom_header">
  <h2>
    Welcome to Freddiemac Developer Portal
  </h2>
</div>
<!-- Breadcrumbs -->
<?php if ((!empty($search_form) || !empty($company_switcher)) && !(drupal_is_front_page()) ): ?>
    <div class="container search-container">
        <div class="row">
            <?php if (!empty($company_switcher)):?>
                <div class="col-xs-6">
                    <div class="apigee-company-switcher-container"><?php print $company_switcher; ?></div>
                </div>
            <?php endif;?>
            <?php if (!empty($search_form)): ?>
                <div class="col-xs-6 pull-right">
                    <?php print render($search_form);?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<!-- Breadcrumbs -->
<?php if ($breadcrumb): ?>
    <div class="container" id="breadcrumb-navbar">
        <div class="row">
            <div class="col-md-12">
                <?php print $breadcrumb;?>
            </div>
        </div>
      </div>
<?php endif; ?>
<div class="row">
<div class="col-md-6">
  <!--<?php print render($page['sidebar_first']);
    ?>-->
  
    <div class="block">
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</br> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</br>
        when an unknown printer took a galley of type and scrambled it to make a type specimen book.</br> It has
        survived not only five centuries,</br>but also the leap into electronic typesetting, remaining essentially unchanged.
      </p>
    </div>
     <div class="block1">
      <p>Contrary to popular belief, Lorem Ipsum is not simply random text.</br> It has roots in a piece of classical Latin literature from 45 BC,</br> making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia,</br> looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, </br>and going through the cites of the word in classical literature, discovered the undoubtable source.
      </div>
</div>
<div class="col-md-6">
    <div class="block2">
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</br> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</br>
        when an unknown printer took a galley of type and scrambled it to make a type specimen book.</br> It has
        survived not only five centuries,</br>but also the leap into electronic typesetting, remaining essentially unchanged.
      </p>
  </div>
     <div class="block3">
      <p>Contrary to popular belief, Lorem Ipsum is not simply random text.</br> It has roots in a piece of classical Latin literature from 45 BC,</br> making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia,</br> looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, </br>and going through the cites of the word in classical literature, discovered the undoubtable source.
      </div>

</div>

</div>
