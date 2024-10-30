<?php

if ( is_admin() ) {
	add_action( 'admin_menu', 'bpps_power_seo_menu' );
	
	function bpps_power_seo_menu() {
		global $bp;
		if ( $bp) {
			add_submenu_page('options-general.php', 'BP Power SEO', 'BP Power SEO', 'manage_options','bp-power-seo','bpps_power_seo_admin' );
			add_action("admin_init", "bpps_register_power_seo_fields");
			add_action("admin_init", "bpps_power_title_fields");
		}
	}
	
}



function bpps_power_seo_admin(){
	global $bpp_title_defaults,$wpdb,$sitemap_update_range;
	$create_map = get_option('create_map');
	$sitemap_update = get_option('sitemap_update');
?>
<h1>BP Power SEO Settings</h1>

<form method="post" action="options.php" class="bpp-form">
<?php
	settings_fields("bp_power_seo_fields");
	do_settings_sections( 'bp_power_seo_fields' );
	
	submit_button(); 

?> 
<div id="bpp-sitemap-form">
<h2>Google XML Sitemap</h2>

<table class="form-table">
	<tr>
		<th scope="row"><label for="create_map">Create XML sitemap</label></th>
		<td><input name="create_map" type="checkbox" value="1"  <?php checked(1,$create_map); ?>/><?php if($create_map){ ?><a target="_blank" href="/bpsitemap/sitemap_index.xml">View Sitemap</a><?php }?></td>
	</tr>
	<tr>
		<th scope="row"><label for="members">Members</label></th>
		<td><input name="members" type="checkbox" value="1" <?php checked(1,get_option('members')) ?>/></td>
	</tr>
	<tr>
		<th scope="row"><label for="groups">Groups</label></th>
		<td><input name="groups" type="checkbox" value="1" <?php checked(1,get_option('groups')) ?>/></td>
	</tr>
	<tr>
		<th scope="row"><label for="searches">Searches/Custom fields</label></th>
		<td><input name="searches" type="checkbox" value="1" <?php checked(1,get_option('searches')) ?>/></td>
	</tr>
	<tr>
		<th scope="row"><label for="sitemap_update">Sitemap Update</label></th>
		<td>
			<select name="sitemap_update" id="sitemap_update">
				<option value="">None</option>
				<?php foreach ($sitemap_update_range as $k=>$v){
					echo '<option value="'.$k.'" '.selected($k,$sitemap_update).'>'.$v.'</option>';
				} 
				
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<td><a href="" id="set-map-def">Set default</a></td>
	</tr>
</table>
</div>
<hr>
<?php
	//submit_button(); 
?> 




<h2>Page title options</h2>
<div id="bpp-title-settings">
<table class="form-table">
	<tr>
		<th scope="row"><label for="bpp_group_list">Groups list</label></th>
		<td><input name="bpp_group_list" type="text" value="<?php echo get_option('bpp_group_list'); ?>"  />
		<br>
		<a href="" class="set-def-title" data-def="<?php echo $bpp_title_defaults['bpp_group_list']; ?>">Set default</a>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="bpp_group_profile">Group profile</label></th>
		<td><input name="bpp_group_profile" type="text" value="<?php echo get_option('bpp_group_profile'); ?>"  />
		<br>
		<a href="" class="set-def-title" data-def="<?php echo $bpp_title_defaults['bpp_group_profile']; ?>">Set default</a>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="bpp_member_list">Members list</label></th>
		<td><input name="bpp_member_list" type="text" value="<?php echo get_option('bpp_member_list'); ?>"  />
		<br>
		<a href="" class="set-def-title" data-def="<?php echo $bpp_title_defaults['bpp_member_list']; ?>">Set default</a>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="bpp_member_profile">Members Profile</label></th>
		<td><input name="bpp_member_profile" type="text" value="<?php echo get_option('bpp_member_profile'); ?>"  />
		<br>
		<a href="" class="set-def-title" data-def="<?php echo $bpp_title_defaults['bpp_member_profile']; ?>">Set default</a>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="bpp_member_profile_tabs">Members Profile (tabs)</label></th>
		<td><input name="bpp_member_profile_tabs" type="text" value="<?php echo get_option('bpp_member_profile_tabs'); ?>"  />
		<br>
		<a href="" class="set-def-title" data-def="<?php echo $bpp_title_defaults['bpp_member_profile_tabs']; ?>">Set default</a>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="bpp_custom_fields">Custom fields</label></th>
		<td><input name="bpp_custom_fields" type="text" value="<?php echo get_option('bpp_custom_fields'); ?>"  />
		<br>
		<a href="" class="set-def-title" data-def="<?php echo $bpp_title_defaults['bpp_custom_fields']; ?>">Set default</a>
		</td>
	</tr>
	<tr>
		<th scope="row"></th><td><a href="" id="show-placeholders">Show placeholders</a>
		<div id="placeholders">
		%%sitename%%<br>
		%%sitedesc%%<br>
		%%post_id%%<br>
		%%groupname%%<br>
		%%parent_title%%<br>
		%%membername%%<br>
		%%tabname%%<br>
		%%searchphrase%%<br>
		%%customfieldname%%
		</div>
	</td>
	</tr>
</table>
</div>

<?php
	$create_schema=get_option('create_schema');
	global $gl_markup_add;
	$schema_fields=get_option('schema_fields');
	//var_dump($schema_fields);
?>
<hr/>
<h2>Rich Snippets</h2>
<div id="bpp-schema-settings">
	<table class="form-table">
		<tr>
			<th scope="row"><label for="create_schema">Enable Rich Snippets</label></th>
			<td><input name="create_schema" type="checkbox" value="1"  <?php checked(1,$create_schema); ?>/></td>
		</tr>
		<?php
			$res=$wpdb->get_results("select id,name from {$wpdb->prefix}bp_xprofile_fields where parent_id=0");
			$f=0;
			foreach($res as $r){
				if($r->id==1) continue;
				echo '
				<tr>
					<th scope="row"><label>'.$r->name.'</label></th>
					<td>
						<select name="schema_fields['.$r->id.']">
							<option value="">None</option>';
						foreach($gl_markup_add as $f){
							echo '<option value="'.$f.'" '.selected($f,$schema_fields[$r->id]).'>'.$f.'</option>';
						}
						
						echo'</option>
					</td>
				</tr>
				';
				$f++;
			}
		?>
	</table>
</div>

<?php
	submit_button(); 
?> 
</form>				

<?php
}

function bpps_power_title_fields(){
	register_setting("bp_power_seo_fields", "bpp_group_list");
	register_setting("bp_power_seo_fields", "bpp_group_profile");
	register_setting("bp_power_seo_fields", "bpp_member_list");
	register_setting("bp_power_seo_fields", "bpp_member_profile");
	register_setting("bp_power_seo_fields", "bpp_member_profile_tabs");
	register_setting("bp_power_seo_fields", "bpp_custom_fields");
	
	register_setting("bp_power_seo_fields", "create_schema");
	register_setting("bp_power_seo_fields", "schema_fields");
}


function bpps_register_power_seo_fields()
{
	
	if($_POST){
		if(!current_user_can('edit_theme_options'))
			die('Need more rights');
	}
	
    register_setting("bp_power_seo_fields", "create_map");
	register_setting("bp_power_seo_fields", "members");
	register_setting("bp_power_seo_fields", "groups");
	register_setting("bp_power_seo_fields", "searches");
	register_setting("bp_power_seo_fields", "sitemap_update");
	
	
	
	if(!isset($_POST['create_map'])){
		unset($_POST['members']);
		unset($_POST['groups']);
		unset($_POST['searches']);
	}
	bpps_update_sitimap();
}

function bpps_update_sitimap($auto_update=false){
	global $wpdb,$bp;
	$str='';

	$user_menus = buddypress()->members->nav->get_item_nav();
	$main_pages=get_option('bp-pages');
	
	
	if($auto_update){
		$_POST['create_map']=get_option('create_map');
		$_POST['members']=get_option('members');
		$_POST['groups']=get_option('groups');
		$_POST['searches']=get_option('searches');
	}
	//var_dump(get_option('create_map'));die();
	if($_POST){
		if(@$_POST['create_map']){
			if(!is_dir(ABSPATH.'bpsitemap')){
				 mkdir (ABSPATH.'bpsitemap',0755);
			}
			if($_POST['members']){
				$query = "SELECT distinct user_id FROM " . $wpdb->prefix . "bp_xprofile_data";
				$users=$wpdb->get_results($query);
				$me=0;
				$members_ar=array();
				if($users){

					
					$str ='<?xml version="1.0" encoding="UTF-8"?>
					<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
					 
					';
					foreach($users as $u){
						if($me==1000){
							$str .='</urlset>';
							file_put_contents(ABSPATH.'bpsitemap/sitemap_members_'. (count($members_ar)+1) .'.xml',$str);
							$members_ar[]='sitemap_members_'. (count($members_ar)+1) .'.xml';
							$str ='<?xml version="1.0" encoding="UTF-8"?>
							<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
							';
							$me=0;
						}
						$user_domain=bp_core_get_user_domain($u->user_id);
						$str .='
							<url>
								<loc>'.$user_domain.'</loc>';
								if(bp_get_user_has_avatar($u->user_id)){
									$url=bp_core_fetch_avatar(array('item_id' => $u->user_id, 'type' => 'thumb', 'width' => 150, 'height' => 150, 'class' => 'friend-avatar','html'=>false));
									if($url){
										$str .='
										<image:image>  
											   <image:loc>'.$url.'</image:loc>  
											   <image:caption>'.bp_core_get_username($u->user_id).'</image:caption>  
										</image:image>';
									}
								}
						
						$str .='</url>';
						foreach($user_menus as $m ){
						if($m['screen_function']=='bp_notifications_screen_unread' or $m['screen_function']=='messages_screen_inbox' || $m['screen_function']=='bp_settings_screen_general')
							continue;
						$str .='
							<url>
								<loc>'.$user_domain.$m['slug'].'/</loc>
							</url>';
							$me++;
						}
						/*$str .='
							<url>
								<loc>'.$user_domain.'media/</loc>
							</url>';*/
						$me++;
						
					}
					$str .='</urlset>';
					file_put_contents(ABSPATH.'bpsitemap/sitemap_members_'. (count($members_ar)+1) .'.xml',$str);
					$members_ar[]='sitemap_members_'. (count($members_ar)+1) .'.xml';
					
				}
			}else{
				foreach (glob(ABSPATH.'bpsitemap/sitemap_members_*') as $filename) {
					unlink($filename);
				}
			}
			
			if($_POST['groups']){
				$query = "SELECT id,name,slug FROM " . $wpdb->prefix . "bp_groups";
				$groups=$wpdb->get_results($query);
				$groups_link = get_the_permalink($main_pages['groups']);
				$gr=0;
				$groups_ar=array();
				if($groups){
					$str ='<?xml version="1.0" encoding="UTF-8"?>
				<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
				';
					foreach($groups as $g){
						if($gr==1000){
							$str .='</urlset>';
							file_put_contents(ABSPATH.'bpsitemap/sitemap_groups_'. (count($groups_ar)+1) .'.xml',$str);
							$groups_ar[]='sitemap_groups_'. (count($groups_ar)+1) .'.xml';
							$str ='<?xml version="1.0" encoding="UTF-8"?>
							<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
							';
							$gr=0;
						}
						
						$avatar_options = array ( 'item_id' =>$g->id, 'object' => 'group', 'type' => 'full', 'avatar_dir' => 'group-avatars', 'alt' => 'Group avatar', 'css_id' => 1234, 'class' => 'avatar', 'width' => 150, 'height' => 150, 'html' => false );
						$group_img = bp_core_fetch_avatar($avatar_options);
						
						$str .='
							<url>
								<loc>'.$groups_link.$g->slug.'/</loc>';
								
							if($group_img){
								$str .='
										<image:image>  
											   <image:loc>'.$group_img .'</image:loc>  
											   <image:caption>'.$g->name.'</image:caption>  
										</image:image>';
							}
						$str.='</url>';
						$str .='
							<url>
								<loc>'.$groups_link.$g->slug.'/members/</loc>
							</url>';
						$str .='
							<url>
								<loc>'.$groups_link.$g->slug.'/media/</loc>
							</url>';
						$gr=$gr+3;
						
					}
					$str .='</urlset>';
					file_put_contents(ABSPATH.'bpsitemap/sitemap_groups_'. (count($groups_ar)+1) .'.xml',$str);
					$groups_ar[]='sitemap_groups_'. (count($groups_ar)+1) .'.xml';
				}
			}else{
				foreach (glob(ABSPATH.'bpsitemap/sitemap_groups_*') as $filename) {
					unlink($filename);
				}
			}
			if($_POST['searches']){
				$searches=array();
				$query = "SELECT f.id,type FROM " . $wpdb->prefix . "bp_xprofile_fields f inner join " . $wpdb->prefix . "bp_xprofile_meta m on m.object_id=f.id WHERE meta_key='do_autolink' AND meta_value='on' AND parent_id=0";

				$res=$wpdb->get_results($query);
				if($res){
					foreach($res as $r){
						if($r->type=='checkbox'){
							$q='select name from '.$wpdb->prefix .'bp_xprofile_fields where parent_id='.$r->id.' AND type="option"';
							$ops=$wpdb->get_results($q);
							if($ops){
								foreach($ops as $o){
									if($o->name)
										$searches[]=$o->name;
								}
							}
						}else{
							$q="SELECT distinct value FROM " . $wpdb->prefix . "bp_xprofile_data where field_id=".$r->id;
							$vals=$wpdb->get_results($q);
							if($vals){
								foreach($vals as $v){
									if($v->value){
										global $field;
										$field= new BP_XProfile_Field( $r->id );
										$html_links=xprofile_filter_link_profile_data($v->value);
										if($html_links){
											$l_a=explode(',',$html_links);
											foreach($l_a as $a){
												preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $a, $result);
												if (!empty($result)) {
													# Found a link.
													if($result['href']){
														$link= $result['href'][0];
														if($link)
															$searches[]=$link;
													}
												}
											 }
										}
										
									}
								}
							}
						}
					}
				}
				$members_link = get_the_permalink($main_pages['members']);
				
				$se=0;
				$searches_ar=array();
				
				
				if($searches){
					$str ='<?xml version="1.0" encoding="UTF-8"?>
				<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
				';
					foreach($searches as $s){
						if($se==1000){
							$str .='</urlset>';
							file_put_contents(ABSPATH.'bpsitemap/sitemap_search_'. (count($searches_ar)+1) .'.xml',$str);
							$searches_ar[]='sitemap_search_'. (count($searches_ar)+1) .'.xml';
							$str ='<?xml version="1.0" encoding="UTF-8"?>
							<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
							';
							$se=0;
						}
						$str .='
							<url>
								<loc>'.$members_link.'?members_search='.$s.'</loc>
							</url>';
						$se++;
					}
					$str .='</urlset>';
					file_put_contents(ABSPATH.'bpsitemap/sitemap_search_'. (count($searches_ar)+1) .'.xml',$str);
					$searches_ar[]='sitemap_search_'. (count($searches_ar)+1) .'.xml';
				}else{
					foreach (glob(ABSPATH.'bpsitemap/sitemap_search_*') as $filename) {
						unlink($filename);
					}
				}
			}
			if(count($members_ar) > 0 || count($groups_ar)>0 || count($searches_ar)>0){
				$str='<?xml version="1.0" encoding="UTF-8"?>
				<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
				if($members_ar){
					foreach($members_ar as $m){
						$str .='
						<sitemap>
						<loc>'.site_url().'/bpsitemap/'.$m.'</loc>
						<lastmod>'.date('Y-m-d').'</lastmod>
						</sitemap>';
					}
				}
				if($groups_ar){
					foreach($groups_ar as $m){
						$str .='
						<sitemap>
						<loc>'.site_url().'/bpsitemap/'.$m.'</loc>
						<lastmod>'.date('Y-m-d').'</lastmod>
						</sitemap>';
					}
				}
				if($searches_ar){
					foreach($searches_ar as $m){
						$str .='
						<sitemap>
						<loc>'.site_url().'/bpsitemap/'.$m.'</loc>
						<lastmod>'.date('Y-m-d').'</lastmod>
						</sitemap>';
					}
				}
				$str .='</sitemapindex>';

			}
			file_put_contents(ABSPATH.'bpsitemap/sitemap_index.xml',$str);
		}else{
			if(is_dir(ABSPATH.'bpsitemap')){
				bpps_deleteDir(ABSPATH.'bpsitemap');
			}
		}
	}
	
}


function bpps_deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            bpps_deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
