<div id="mainContainer" class="boxShadow clearfix">
  <div id="main" class="clearfix">
    <h2>
      <?php echo $page_left_column_headline; ?>
    </h2>
    <div class="leftCol">
      <div class="documents">
        <h3>
        	<?php echo $page_left_column_paragraph; ?>
        </h3>
<?php
$data_sheet_id=null;
if (!empty($page_data_sheets )):
	echo '<ul class="datasheets">';

				
	if ( count($page_data_sheets) > 0 ) :
		
		$ds = new Pod('data_sheets');
		$ds->findRecords(array('limit'=>'-1'));
		
		$total_ds = $ds->getTotalRows();
		
		if( $total_ds>0 ) : 
			
			while ( $ds->fetchRecord() ) : 
				
				$ds_id = $ds->get_field('id');
				foreach( $page_data_sheets as $k=>$v ) : 
					
					
					if(isset($v['id']) && $v['id'] == $ds_id) :											
						$ds_url   = $ds->get_field('file');
							$ds_url = $ds_url[0]['guid'];
						//print_r($ds_url);	
						echo '<li>';
									echo '<a href="' .$ds_url. '" title="' . $v['name']  .'" target="_blank">';
									echo $v['name'];
									echo '</a>';
									echo '<span class="metadata">' . $v['file_format'] . ', ' . $v['file_size'] . '</span>';
									//print_r( $file_url );
									echo '</li>'; 
		endif; endforeach; endwhile; endif; endif; 
		echo '</ul>';	
	endif;			
?>
        <?php if (!empty($page_left_column_image)) : ?>
        	<img src="<?php echo $page_left_column_image; ?>" alt="<?php echo $page_left_column_image_name; ?>" />
         <?php endif; ?>
      </div>
      <?php if (!empty($page_left_column_button_heading) || (!empty($page_left_column_button_label) && !empty($page_left_column_button_url))) : ?>
        <div class="Info">
          <?php if (!empty($page_left_column_button_heading)) : ?>
          <span class="button_heading">
            <?php echo $page_left_column_button_heading; ?>
          </span>
          <?php endif; ?>
          <?php
          if(!empty($page_left_column_button_url) && !empty($page_left_column_button_label)) {
            echo '<a class="button" href="' . $page_left_column_button_url . '" name="' . $page_left_column_button_label . '">' . $page_left_column_button_label . '</a>';
          }
          ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="rightCol">
      <div style="width:47%; float: left; padding-right: 6%; display: inline;" class="post_column_1">
        <?php echo $page_center_column_text; ?>
      </div>
      <div style="width:45%; float: left; padding-right: 0%; display: inline;" class="post_column_1">
        <?php echo $page_right_column_text; ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
  </div>
</div><!-- end #mainContainer -->