<?php 
/*
Plugin Name: Médiatheque plugin 
 */

add_action('init','post_mediatheque');
function post_mediatheque()
{

	register_post_type('mediax',array(
			'label'=>'Médiathéque',
			'public'=>true,
			'show_in_menu'=> true,
			'supports'=>array('title'),

		));
}

add_action('add_meta_boxes','metaBoxF',10,2);

function metaBoxF()
{
	  add_meta_box( 
        'metaboxapp',
        __( 'Informations' ),
        'imadox',
        'mediaX'
       
    );
}


function imadox()
{
	global $post;
	$type = get_post_meta($post->ID,'type',true);
	$image = get_post_meta($post->ID,'image',true);
	$video_url = get_post_meta($post->ID,'video_url',true);

	?>
		<label for="type">Type</label>
		<select name="type" id="type">
			<option>-----</option>
			<option value="1" <?php if($type==1) echo "selected"; ?> >Image</option>
			<option value="2" <?php if($type==2) echo "selected"; ?>>Video youtube</option>
		</select>

		<div class="image_1" style="display: none;">
		<label for="image">Image</label>
			<input type="file" name="image" id="image" />
			<div class="apercu_image">
				
				<?php

					if(!empty($image) && file_exists(get_home_path().$image))
					{
						?>
						<img src="<?php echo  get_site_url() ."/".$image; ?>" style='width:200px; height:190px;'/>
						<?php 
					}
				?>
			</div>
		</div>

		<div class='youtube_video' style="display: none;">
		<label for="youtube_url">Youtube Url:</label>
				<input type="text" name="video_url" id="youtube_url" placeholder="https://www.youtube.com/watch?v=aOSacabfYSI" style="width:100%;" value="<?php echo $video_url; ?>" ><br/>
				<div class="thumbnail" style="margin-top: 10px; ">
					
				</div>
		</div>


		<script type="text/javascript">
			jQuery(function($){
				$(document).on('change','#type',function(){
					var data = $(this).val();
					if(data == 1)
					{
						$(".image_1").show();
						$(".youtube_video").hide();
					}else if(data == 2){
						$(".image_1").hide();
						$(".youtube_video").show();
					}else{
						$(".image_1").hide();
						$(".youtube_video").hide();
					}
					return false;
				});
				$("#youtube_url").focusout(function(){
						var data = $(this).val();
						var code_video = data.split("watch?v=");
						if(code_video[1])
						{
							code_video = code_video[1];
						}
						$(".thumbnail").html('<img src="https://img.youtube.com/vi/'+code_video+'/default.jpg" />');
				});
				<?php if(!empty($video_url)): ?>
					$("#youtube_url").focusout();
				<?php endif; ?>
				<?php if(!empty($type)): ?>
					$("#type").change();
				<?php endif; ?>

			

			});
		</script>
	<?php

}

add_action('save_post','saveMeta');

function saveMeta($post)
{
	if(isset($_POST['type']))
	{
		update_post_meta($post,'type',$_POST['type']);
	update_post_meta($post,'video_url',$_POST['video_url']);
	//	update_post_meta($post,'type',$_POST['type']);
	if(isset($_FILES['image']) && $_FILES['image']['error'] == 0)
				{
					$ext = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
					$ext = strtolower($ext);
					if(in_array($ext,array('jpg','png','jpeg')))
					{
							$imageProduit = time().".".$ext;
							if(!is_dir(get_home_path().'/images_app/'))
							{
								mkdir(get_home_path().'/images_app/',0776);
							}
							if(move_uploaded_file($_FILES['image']['tmp_name'],get_home_path().'/images_app/'.$imageProduit))
							{
								update_post_meta($post,'image',"/images_app/".$imageProduit);
							}
					}



				}
	}
	
}

/** Activer Upload **/
function update_edit_form() {
    echo ' enctype="multipart/form-data"';
} // end update_edit_form
add_action('post_edit_form_tag', 'update_edit_form');

add_action('manage_mediax_posts_custom_column','ihaaaaab',10,2);

function ihaaaaab($column,$post_id)
{
	global $post;
	switch ($column) {
		case 'title':
			the_title();

			break;
		case 'image':
		
			$type = get_post_meta($post_id,'type',true);

			if($type == 1)
			{
				$image = get_post_meta($post_id,'image',true);

				echo "<img src='".get_site_url()."/".$image."' style='width:100%' />" ;
			}elseif($type == 2)
			{
				$videoU = get_post_meta($post_id,'video_url',true);

				$video_url = explode("watch?v=",$videoU);
				if(isset($video_url[1]))
				{
					echo "<img src='https://img.youtube.com/vi/".$video_url[1]."/default.jpg' style='width:100%' />";
				}
			}
		break;
	}
}
add_filter('manage_mediax_posts_columns','lo2ay');
function lo2ay($columns)
{
	$columns = array(
		'title'=>'Titre',
		'image'=>'Image'
		);
	return $columns;
}