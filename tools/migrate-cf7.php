<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require __DIR__ . '/../../../../wp-load.php';
if (!class_exists('WPCF7_ContactForm')) { throw new Exception('CF7 is inactive'); }
if (get_option('foods_cf7_forms')) { throw new Exception('Migration already completed. Do not overwrite managed forms.'); }
$definitions = [];
foreach (['contact','business','job','graduate','career'] as $kind) {
 ob_start();
 if ($kind === 'contact') { include get_template_directory().'/page-contact.php'; }
 elseif ($kind === 'business' || $kind === 'job') { $business_form_type=$kind==='job'?'entry':'contact'; include get_template_directory().'/template-parts/company-business-form.php'; }
 else { include get_template_directory().'/page-recruit-entry.php'; }
 $html=ob_get_clean();
 preg_match('/<form\b[^>]*>(.*?)<\/form>/s',$html,$m);
 if (empty($m[1])) throw new Exception('Form missing '.$kind);
 $form=$m[1];
 $form=preg_replace('/<input[^>]*type="hidden"[^>]*>/s','',$form);
 $radio=[];
 preg_match_all('/<input[^>]*type="radio"[^>]*>/',$form,$radios);
 foreach($radios[0] as $input) { preg_match('/name="([^"]+)"/',$input,$n); preg_match('/value="([^"]+)"/',$input,$v); $radio[$n[1]][]=$v[1]; }
 foreach($radio as $name=>$values) {
  $tag='[radio '.$name.' use_label_element default:'.($kind==='career'?3:1).' "'.implode('" "',$values).'"]';
  if($name==='contact_store') { $form=preg_replace('/<div class="p-contact-store__options">.*?<\/div>\s*<p class="p-contact-store__business-note">/s','<div class="p-contact-store__options">'.$tag.'</div><p class="p-contact-store__business-note">',$form); }
  else { $form=preg_replace('/<div>\s*<label><input type="radio".*?<\/div>/s','<div>'.$tag.'</div>',$form); }
 }
 $form=preg_replace_callback('/<select\b([^>]*)>(.*?)<\/select>/s',static function($m) { preg_match('/name="([^"]+)"/',$m[1],$n); preg_match_all('/<option[^>]*value="([^"]+)"[^>]*>/', $m[2],$o); $v=array_filter($o[1],static fn($v)=>$v!==''); return '[select'.(str_contains($m[1],'required')?'*':'').' '.$n[1].' include_blank "'.implode('" "',$v).'"]'; },$form);
 $form=preg_replace_callback('/<textarea\b([^>]*)>.*?<\/textarea>/s',static function($m) { preg_match('/name="([^"]+)"/',$m[1],$n); return '[textarea'.(str_contains($m[1],'required')?'*':'').' '.$n[1].(str_contains($m[1],'is-small')?' class:is-small':'').']'; },$form);
 $form=preg_replace_callback('/<input\b([^>]*)>/s',static function($m) { preg_match('/name="([^"]+)"/',$m[1],$n); if(!$n)return ''; preg_match('/type="([^"]+)"/',$m[1],$t); $type=$t[1]; if($type==='checkbox') return '[checkbox* '.$n[1].' "1"]'; preg_match('/id="([^"]+)"/',$m[1],$id); preg_match('/class="([^"]+)"/',$m[1],$cl); $opts=$id?' id:'.$id[1]:''; if($cl)foreach(explode(' ',$cl[1]) as $c)$opts.=' class:'.$c; return '['.$type.(str_contains($m[1],'required')?'*':'').' '.$n[1].$opts.']'; },$form);
 $definitions[$kind]=$form;
}
$backup=[];
foreach(get_posts(['post_type'=>'wpcf7_contact_form','posts_per_page'=>-1]) as $p){ $f=WPCF7_ContactForm::get_instance($p->ID); $backup[]=['id'=>$p->ID,'title'=>$p->post_title,'properties'=>$f->get_properties()]; }
$backupDir='C:/AI-KOS/backups/foods-selectioncojp'; if(!is_dir($backupDir))wp_mkdir_p($backupDir);
file_put_contents($backupDir.'/cf7-before-migration-'.gmdate('Ymd-His').'.json',wp_json_encode($backup,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
$map=[];
foreach($definitions as $kind=>$definition){
 $f=WPCF7_ContactForm::get_template();
 $titles=['contact'=>'店舗・業者様お問い合わせ','business'=>'企業向けお問い合わせ','job'=>'パートアルバイト応募','graduate'=>'新卒採用応募','career'=>'キャリア採用応募'];
 $f->set_title($titles[$kind]);
 $props=$f->get_properties(); $props['form']=$definition;
 $fields=[]; preg_match_all('/\[(?:text|email|tel|number|textarea|select|radio|checkbox)\*?\s+([^\s\]]+)/',$definition,$matches);
 foreach(array_unique($matches[1]) as $name){if(!in_array($name,['email_confirm','your_email_confirm','privacy_agree'],true))$fields[]=$name.'：['.$name.']';}
 $email=$kind==='contact'?'your_email':'email';
 $props['mail']=array_merge($props['mail'],['active'=>true,'recipient'=>'k.fukushige@cdrive-inc.jp','sender'=>'セレクション <wordpress@foods-selectioncojp.local>','subject'=>'【開発環境】'.$titles[$kind],'body'=>implode("\n\n",$fields),'additional_headers'=>'Reply-To: ['.$email.']','use_html'=>false]);
 $props['mail_2']=array_merge($props['mail_2'],['active'=>true,'recipient'=>'k.fukushige@cdrive-inc.jp','sender'=>'セレクション <wordpress@foods-selectioncojp.local>','subject'=>'【開発環境】'.$titles[$kind].'受付','body'=>"受け付けました。内容を確認のうえ、担当者よりご連絡いたします。\n\n".implode("\n\n",$fields),'additional_headers'=>'','use_html'=>false]);
 $f->set_properties($props); $f->save(); $map[$kind]=$f->id();
}
update_option('foods_cf7_forms',$map,false);
echo wp_json_encode($map,JSON_UNESCAPED_UNICODE)."\n";
