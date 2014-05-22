<div id='action<? echo $action_id; ?>RelevanceDiv' style='display: inline;'>
<?php
	for($i=1; $i<=5; $i++){
		if ($relevance >= $i){
			$starImage = 'star-on.png';			
		} else {
			$starImage = 'star-off.png';
		}
		$divToUpdate = 'action'.$action_id.'RelevanceDiv';
		
		echo str_replace(
			'[[REPLACE]]', 
			$html->image('/img/'.$starImage, array('width' => 16, 'height' => 16, 'title' => $multilang->text('GiveRelevanceTooltip', $i))),
			$ajax->link(
				'[[REPLACE]]',
				array( 'controller' => 'actions', 'action' => 'ajax_setRelevance', $action_id, $i),     
				array( 'update' => 'action'.$action_id.'RelevanceDiv')
			)
		);			
	}
?>
</div>