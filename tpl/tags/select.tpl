<select name="<?= $name ?>" <? if (isset($onchange)) :?>onChange="<?= $onchange ?>"<? endif ?>>
<? foreach ($options as $value => $text) : ?>
	<option value="<?= $value ?>" <?= $value == $default ? 'SELECTED' : '' ?>><?= $text ?></options>
<? endforeach ?>
</select>