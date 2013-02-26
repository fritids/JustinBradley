<?php

/* Template Name: Leadership */

get_header(); 

$currentpage = 'leadership';
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php include(TEMPLATEPATH."/inc/top-two-col.php");?>
<?php
$team = new Pod('team');
$team->findRecords(array('orderby'=>'CAST(t.order as UNSIGNED) asc', 'limit'=>'-1'));
$total_teams = $team->getTotalRows();
if( $total_teams>0 ) :
	while ( $team->fetchRecord() ) :
		
		$team_name      = $team->get_field('name');
		$column_counter = 0;
		$people = new Pod('people');
		$people->findRecords(array('orderby'=>'CAST(t.order as UNSIGNED) asc', 'limit'=>'-1'));
		$total_people = $people->getTotalRows();
	 ?>
    <div id="mainContainer" class="boxShadow clearfix">
  		<div id="main" class="clearfix">
    		<h2>
        	<?php echo $team_name; ?>
        </h2>
      	<?php while ( $people->fetchRecord() ) : 
          $people_team      = $people->get_field('team');
          $people_slug      = $people->get_field('slug');
          
          $people_name      = $people->get_field('name');
          $people_email  = $people->get_field('email');
          $people_image     = $people->get_field('image');
						$people_image = $people_image[0]['guid'];
					
					$people_vcard     = $people->get_field('vcard');
						$people_vcard = $people_vcard[0]['guid'];
						
          $people_bio       = $people->get_field('bio');
						$people_bio = word_trim($people_bio, 30);
          $people_title     = $people->get_field('title');

          // data cleanup
          $people_bio       = wpautop( $people_bio );
          $people_team 			= $people_team[0][name]; // PICK field
         ?>
        <?php if( $people_team == $team_name): ?> 
          <div class="member <?php echo " column".$column_counter; ?>">
          
            <?php if( !empty( $people_image ) ) : ?>
              <a href="<?php echo $people_slug; ?>">
                <img src="<?php echo $people_image; ?>" alt="Photo of <?php echo $people_name; ?>" class="" />
              </a>
            <?php  endif; ?>
            
            <h2><a href="<?php echo $people_slug; ?>"><?php echo $people_name; ?></a></h2>
            <span class="title"><?php echo $people_title; ?></span>
            <?php echo $people_bio; ?>
          </div>
          <?php
					
          if ($column_counter==2){
            echo '<div class="clear">&nbsp;</div>';
            $column_counter = 0;
          } else {
            $column_counter = $column_counter+1;
          }
					
        endif; 
        ?>
      <?php   endwhile;   ?>
      </div>
    </div>
    <?php endwhile; endif; ?>
<?php endwhile; endif; ?> 
<?php get_footer(); ?>