<?php

function bpps_groups_title( $title ){
	global $bp,$post,$wpdb;
	$id=get_the_ID();
	$main_pages=get_option('bp-pages');
	
	/*groups list*/

	$rules['%%sitename%%']=get_bloginfo( 'name');
	$rules['%%sitedesc%%']=get_bloginfo('description');
	$rules['%%post_id%%']='';
	$rules['%%postname%%']='';
	$rules['%%groupname%%']='';
	$rules['%%membername%%']='';
	$rules['%%tabname%%']='';
	$rules['%%searchphrase%%']='';
	$rules['%%customfieldname%%']='';
	if($id){
		$rules['%%post_id%%']=$id;
	}
	if(@$post->post_title){
		$rules['%%postname%%']=$post->post_title;
	}
	
	//var_dump(bp_current_component());

	/*Groups list*/
	if($bp->current_component=='groups' && !$bp->current_item && !$bp->current_action){
		$groups_template=get_option('bpp_group_list');
		if($groups_template){
			unset($title);
			$title[]=bpps_general_title_replace($rules,$groups_template);
		}
	}
	
	/*Members list*/
	if($bp->current_component=='groups' && $bp->current_action=='members' && $bp->groups->current_group){
		$member_list_template=get_option('bpp_member_list');
		$rules['%%groupname%%']=$bp->groups->current_group->name;
		if($member_list_template){
			unset($title);
			$title[]=bpps_general_title_replace($rules,$member_list_template);
		}
	}
	
	/*Group profile*/
	if($bp->current_component=='groups' && $bp->current_action=='home' && $bp->groups->current_group){
		$group_template=get_option('bpp_group_profile');
		$rules['%%groupname%%']=$bp->groups->current_group->name;
		if($group_template){
			unset($title);
			$title[]=bpps_general_title_replace($rules,$group_template);
		}
	}
	
	
	/*Member profile*/
	if(bp_is_user() && bp_current_action()=='just-me'){
		$member_profile_template=get_option('bpp_member_profile');
		if($member_profile_template){
			$rules['%%membername%%']=$bp->displayed_user->userdata->display_name;
			unset($title);
			$title[]=bpps_general_title_replace($rules,$member_profile_template);
			
		}
	}
	/*Member profile tabs*/
	if(bp_is_user()  && bp_current_action()!='just-me'){
		$member_profile_tabs_template=get_option('bpp_member_profile_tabs');
		if($member_profile_tabs_template){
			//var_dump(bp_get_nav_menu_items());die();
			$name='';
			$rules['%%membername%%']=$bp->displayed_user->userdata->display_name;
			
			foreach(bp_get_nav_menu_items() as $v){
				if(!isset($v->class[1])) continue;
				if($v->class[1]=='current-menu-parent')
					$name=$v->name;
			}
			
			if(strpos($name,' <'))
				$tab_name=trim(substr($name,0,strpos($name,' <')));
			else
				$tab_name=$name;
			$rules['%%tabname%%']=$tab_name;
			///var_dump($tab_name);die();
			unset($title);
			$title[]=bpps_general_title_replace($rules,$member_profile_tabs_template);
		}
	}
	/*Searches*/
	if(isset($_GET['members_search'])){
		$search_template=get_option('bpp_custom_fields');
		if($search_template){
			$field_name='';
			$rules['%%searchphrase%%']=$_GET['members_search'];
			if(strpos($search_template,'customfieldname')){
				$q='Select f2.name from '.$wpdb->prefix.'bp_xprofile_fields f1 inner join '.$wpdb->prefix.'bp_xprofile_fields f2 on f1.parent_id=f2.id where f1.name="'.$_GET['members_search'].'"';
				$field_name=$wpdb->get_var($q);
				//var_dump($field_name);die();
				if(!$field_name){
					$q='select f.name from '.$wpdb->prefix.'bp_xprofile_fields f inner join '.$wpdb->prefix.'bp_xprofile_data d on f.id=d.field_id where d.value="'.$_GET['members_search'].'"';
					$field_name=$wpdb->get_var($q);
				}
			}
			$rules['%%customfieldname%%']=$field_name;
			unset($title);
			$title[]=bpps_general_title_replace($rules,$search_template);
		}
	}
	
	return $title;

}
if(!is_admin()){
add_filter('document_title_parts', 'bpps_groups_title', 99);
}

function bpps_general_title_replace($rules,$template){
	foreach($rules as $k=>$v){
		$template = str_replace($k,$v,$template);
	}
	$template = str_replace('  ',' ',$template);
	
	return $template;
}


function bpps_schema( $new ) { 
	if(get_option('create_schema')){
		global $bp,$wpdb;
		if(bp_is_user() && (bp_current_action()=='just-me' || bp_current_action()=='public')){
		
			$params=array();
			$uid=$bp->displayed_user->id;
			if((int)$uid){
				echo '<script type="application/ld+json">
				{
				  "@context": "http://schema.org",
				  "@type": "Person",';
				$fname = $wpdb->get_var("select meta_value from {$wpdb->prefix}usermeta where meta_key='first_name' AND user_id=$uid");
				if($fname)
					$params[]='"givenName":"'.$fname.'"';
				$lname = $wpdb->get_var("select meta_value from {$wpdb->prefix}usermeta where meta_key='last_name' AND user_id=$uid");
				if($lname)
					$params[]='"familyName":"'.$lname.'"';
				$image=bp_core_fetch_avatar(array('item_id' => $uid, 'type' => 'thumb', 'width' => 150, 'height' => 150, 'class' => 'friend-avatar','html'=>false));
				if($image)
					$params[]='"image":"'.$image.'"';
				$params[]='"url":"'.bp_core_get_user_domain($uid).'"';
				$zap="select name from {$wpdb->prefix}bp_groups_members gm inner join {$wpdb->prefix}bp_groups g on gm.group_id=g.id where user_id=$uid";
				//echo $zap;die();
				$groups = $wpdb->get_results($zap);				
				if($groups){
					foreach($groups as $g){
							$groups_ar[] = '{"@type":"Organization","name":"'.$g->name.'"}';
						}
					$params[]='"memberOf":['.implode(',',$groups_ar).']';
				}
				
				$friends1=array();
				$friends2=array();
				
				$fr1=$wpdb->get_results("select display_name from {$wpdb->prefix}bp_friends f inner join {$wpdb->prefix}users um on f.initiator_user_id=um.ID where friend_user_id=$uid ");
				foreach($fr1 as $f){
					$friends1[]=$f->display_name;
				}
			
				$fr2=$wpdb->get_results("select display_name from {$wpdb->prefix}bp_friends f inner join {$wpdb->prefix}users um on f.friend_user_id=um.ID where initiator_user_id=$uid ");
				foreach($fr2 as $f){
					$friends2[]=$f->display_name;
				}
				$friends_arr=array_merge($friends1,$friends2);
				
				if($friends_arr){
					$params[]='"knows":["'.implode('","',$friends_arr).'"]';
				}
				
				$schema_fields=get_option('schema_fields');
				foreach($schema_fields as $fid=>$field_name){
					if(!$field_name) continue;
					$f_name=$field_name;
					if(strpos($field_name,')'))
						$f_name=trim(substr($field_name,0,strpos($field_name,'(')));
					$res=$wpdb->get_var("select value from {$wpdb->prefix}bp_xprofile_data where user_id=$uid AND field_id=$fid");
					if($res){
						if(is_array(@unserialize($res))){
							$val_ar=array();
							$res=unserialize($res);

							foreach($res as $v){
								if($v)
									$val_ar[]=$v;	
							}
							
							$params[]='"'.$f_name.'":["'.implode('","',$val_ar).'"]';
						}else{
							$params[]='"'.$f_name.'":"'.$res.'"';
						}
						
					}
				}
				
				
				echo implode(',',$params);
				echo '}</script>';

			}
		}
		
		if($bp->current_component=='groups' && $bp->current_action=='home'){
			$group_id= $bp->groups->current_group->id;
			$zap="select display_name from {$wpdb->prefix}users where ID={$bp->groups->current_group->creator_id}";
			$admin=$wpdb->get_var($zap);
			
			$add_str='';
			
			$group_cover_image_url = bp_attachments_get_attachment('url', array(
			  'object_dir' => 'groups',
			  'item_id' => $group_id,
			));
			if($group_cover_image_url)
				$add_str .=',"image":"'.$group_cover_image_url.'"';
			
			$avatar_options = array ( 'item_id' =>$group_id, 'object' => 'group', 'type' => 'full', 'avatar_dir' => 'group-avatars', 'alt' => 'Group avatar', 'css_id' => 1234, 'class' => 'avatar', 'width' => 150, 'height' => 150, 'html' => false );
			$group_logo = bp_core_fetch_avatar($avatar_options);
			if($group_logo)
				$add_str .=',"logo":"'.$group_logo.'"';
			//$zap="select display_name from {$wpdb->prefix}bp_groups_members gm inner join {$wpdb->prefix}users u on u.ID=gm.user_id where group_id=$group_id AND u.ID<>{$bp->groups->current_group->creator_id}";
			
			$zap="select display_name from {$wpdb->prefix}bp_groups_members gm inner join {$wpdb->prefix}users u on u.ID=gm.user_id where group_id=$group_id";
			$members_ar=$wpdb->get_col($zap);
			if($members_ar){
				foreach($members_ar as $m){
					$members_ar2[] = '{"@type":"Person","name":"'.$m.'"}';
				}
				$add_str .=',"members":['.implode(',',$members_ar2).']';
			}
			
			echo '<script type="application/ld+json">
				{
				  "@context": "http://schema.org",
				  "@type": "Organization",
				  "legalName":"'.$bp->groups->current_group->name.'",
				  "url":"'.$bp->canonical_stack['base_url'].'",
				  "description":"'.$bp->groups->current_group->description.'",
				  "founder":"'.$admin.'"'.$add_str;
				  
			echo '}</script>';

				 
		}
	}
	//
	
}       
// add the action 
add_action( 'wp_head', 'bpps_schema', 10, 1 ); 


add_action( 'init', 'bpps_run_update_sitemap',100 );
function bpps_run_update_sitemap() {
	
	$sitemap_update = get_option('sitemap_update');
	if($sitemap_update){
		if((@filemtime(ABSPATH.'bpsitemap/sitemap_index.xml')+ $sitemap_update) < time()){
			bpps_update_sitimap(true);
		}
			
	}
}