<?php

/* Template Name: Team Member */

get_header(); 

$currentpage = 'team-member';


$found_member = false;

global $pods;
$person_slug  = pods_url_variable(-1);
$person       = new Pod('people', $person_slug);

if( !empty( $person->data ) )
{
	$found_member = true;
	$person_order         = $person->get_field('order');
	$person_name       = $person->get_field('name');
	$person_title   = $person->get_field('title');
	$person_photo      = $person->get_field('image');
	$person_bio        = $person->get_field('bio');
	$person_email        = $person->get_field('email');
	$person_vcard       = $person->get_field('vcard');
	$person_team		= $person->get_field('team'); 
	 $person_bio        = wpautop( $person_bio );
 	$person_photo      = $person_photo[0]['guid'];
	$person_subject 		= $person->get_field('subject');
}
?>
<div id="mainContainer" class="boxShadow clearfix team-member">
  		<div id="main" class="clearfix">
    		
    <?php if( $found_member ) : ?>

      <div class="back_link"><a href="javascript:javascript:history.go(-1)">&lsaquo;&nbsp; BACK TO <?php print_r( $person_team[0]['name'] ); ?></a></div>
      	
        <div class="leftCol">
        	<?php if( !empty($person_photo) ) : ?>
            	<img src="<?php echo $person_photo; ?>" alt="" />
            <?php endif ?>
            <?php if( !empty($person_vcard) || !empty($person_email)) : ?>
            	<h2>Connect:</h2>
            	<?php if( !empty($person_email) ) : ?>
					<a href="mailto:<?php echo $person_email; ?>?subject=<?php echo $person_subject; ?>"><?php echo $person_email; ?></a>
                <?php endif ?>
                <?php if( !empty($person_vcard) ) : ?>
                	<a href="<?php echo $person_vcard; ?>"><?php echo $person_vcard; ?></a>
                <?php endif ?>
            <?php endif ?>
        </div>
        <div class="rightCol">
      	
            <h2><?php echo $person_name; ?></h2>
            <div class="entry">
              <h4><?php echo $person_title; ?></h4>
              
              <?php echo $person_bio; ?>
            </div>
         </div>
      </div>

    <?php else: ?>

      <div class="post">
	    <h2>Team Member Not Found</h2>
	    <div class="entry">
	      <p>Sorry, that Team member could not be found!</p>
	    </div>
	  </div>
    <?php endif ?>
  </div>
<?php get_footer(); ?>