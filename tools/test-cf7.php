<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require __DIR__.'/../../../../wp-load.php';
$kind=$argv[1]; $form=foods_cf7_form($kind);
$_SERVER['HTTP_USER_AGENT']='LocalWP form integration test'; $_SERVER['REMOTE_ADDR']='127.0.0.1'; $_SERVER['REQUEST_URI']='/wp-admin/admin-post.php'; $_SERVER['REQUEST_METHOD']='POST';
$_POST=[];
foreach($form->scan_form_tags() as $tag){if(!$tag->name)continue;$_POST[$tag->name]=$tag->basetype==='email'?'test@example.com':($tag->basetype==='number'?'25':($tag->basetype==='url'?'https://example.com':($tag->basetype==='tel'?'09012345678':(in_array($tag->basetype,['select','radio','checkbox'],true)?$tag->values[0]:'テスト'))));}
$_POST['_wpcf7']=(string)$form->id();$_POST['_wpcf7_unit_tag']='wpcf7-f'.$form->id().'-o1';$_POST['_wpnonce']=wpcf7_create_nonce();
$mail=[];add_filter('pre_wp_mail',static function($return,$args)use(&$mail){$mail[]=$args;return true;},10,2);
if(!foods_cf7_preview_validate($kind))throw new Exception('Preview validation failed');
$result=$form->submit();
if($result['status']!=='mail_sent')throw new Exception(wp_json_encode($result));
if(count($mail)!==2)throw new Exception('Expected notification and receipt');
foreach($mail as $m){if($m['to']!=='k.fukushige@cdrive-inc.jp')throw new Exception('Wrong recipient');if(str_contains($m['message'],'[email]'))throw new Exception('Unexpanded tag');}
echo $kind.': CF7 validation and both mail templates passed; no real mail sent.'."\n";
