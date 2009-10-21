<!-- begin:common/data_list -->

<? if (is_array($columns) and is_array($list)) : ?>
<form>
<table>
	<thead>
		<tr>
			<th><input type="checkbox" id="all_checked" onClick="javascript:checkAll();"/></th>
			<? foreach ($columns as $col_key => $col_title) : ?>
			<th><?= $col_title ?></th>
			<? endforeach ?>
			<? if (is_array($actions)) : ?>
			<th>&nbsp;</th>
			<? endif ?>
		</tr>
	</thead>
	<tbody>
		<? $odd = false; foreach ($list as $row) : $odd = !$odd ?>
		<tr class="row_<?= $odd ? 'odd' : 'even' ?>">
			<td class="list_checkbox"><input type="checkbox" value="<?=$row['id']?>" name="id[]" onClick="javascript:checkOne();"/></td>
			<?
				foreach (array_keys($columns) as $col_key)
				{
					$field_data = array_key_exists($col_key, $row) && !empty($row[$col_key]) ? $row[$col_key] : '&nbsp;';
					
					if (is_array($columns_actions) && array_key_exists($col_key, $columns_actions))
					{
						$action = $columns_actions[$col_key];
						$action_args = array
						(
							'url'		=> $action_path . '/' . $action . '/' . $row['id'],
							'confirm'	=> $action_confirm[$action],
							'content'	=> $field_data
						);
			?>
			<td><?= tag('anchor', $action_args) ?></td>
			<?
					}
					else
					{
			?>
			<td><?= $field_data ?></td>
			<?
					}
				}
			?>

			<? if (is_array($actions)) : ?>
			<td class="list_actions"><?

				foreach($actions as $action => $action_text)
				{
					$action_args = array
					(
						'url'		=> $action_path . '/' . $action . '/' . $row['id'],
						'confirm'	=> $action_confirm[$action],
						'content'	=> tag('img', array('src' => "images/icons/actions/$action.png"))
					);
	
					echo tag('anchor', $action_args);
				}

			?></td>
			<? endif ?>
		</tr>
		<? endforeach ?>
		<? if (array_key_exists('delete', $actions)) : ?>
		<tr>
			<td><img src="images/icons/arrow.png"/></td>
			<td colspan="<?= count($columns) + 1 ?>"><input type="submit" name="delete" value="Borrar seleccionados" onClick="return confirm('Borrar los registros seleccionados?')"/></td>
		</tr>
		<? endif ?>
	</tbody>
</table>
</form>
<? endif ?>

<SCRIPT language="JavaScript" type="text/javascript">
<!--

	function hasChecked ()
	{
		var elements = document.getElementsByName('id[]');
		
		for (var i = 0; i < elements.length; i++)
			if (elements[i].checked) return true;

		return false;
	}

	function checkOne ()
	{
		var elements = document.getElementsByName('id[]');
		var all_checked = true;
		
		for (var i = 0; i < elements.length; i++)
			if (!elements[i].checked) all_checked = false;
		
		document.getElementById('all_checked').checked = all_checked;
	}

	function checkAll ()
	{
		var elements = document.getElementsByName('id[]');
		var all_checked = document.getElementById('all_checked').checked;

		for (var i = 0; i < elements.length; i++)
			elements[i].checked = all_checked;
	}

//-->
</SCRIPT>

<!-- end:common/data_list -->