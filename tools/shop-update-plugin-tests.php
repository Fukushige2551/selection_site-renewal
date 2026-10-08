<?php
define('ABSPATH', __DIR__);
function wp_json_encode($value) { return json_encode($value); }
require __DIR__ . '/shop-update-plugin/updater.php';
function verify($ok, $name) { if (!$ok) { throw new RuntimeException($name); } echo 'PASS: ' . $name . PHP_EOL; }
$rows=[['title'=>'花野井店','slug'=>'hananoi']];
$post=(object)['ID'=>90,'post_title'=>'花野井店','post_name'=>'花野井店','post_status'=>'publish'];
verify(fsu_match($rows,[$post])[0]['id']===90,'Title matching preserves target ID');
foreach ([[],[$post,clone $post],[(object)['ID'=>90,'post_title'=>'花野井店','post_name'=>'hananoi','post_status'=>'draft']],[$post,(object)['ID'=>91,'post_title'=>'別店舗','post_name'=>'hananoi','post_status'=>'publish']]] as $i=>$posts) {
    $blocked=false;
    try { fsu_match($rows,$posts); } catch (RuntimeException $e) { $blocked=true; }
    verify($blocked,'Missing / duplicate / unpublished / conflicting target blocked: ' . $i);
}
verify(fsu_equal(['visa','jcb'],['jcb','visa']),'Checkbox order is not a change');
verify(!fsu_equal(['visa'],['visa','jcb']),'Changed choices detected');
verify(fsu_equal("駅\r\n徒歩", "駅\n徒歩"),'Line endings normalized');
verify(fsu_digest($rows,['slug'=>'old']) !== fsu_digest($rows,['slug'=>'new']),'Stale snapshot detectable');
