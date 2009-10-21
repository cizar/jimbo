<? if ($paginator->has_pages()) : ?>

	<?
		isset($prev_page_label)
			or $prev_page_label = "Prev";

		isset($next_page_label)
			or $next_page_label = "Next";
	?>

	<? if ($paginator->has_prev_page()) : ?>
		<a href="<?= $_SERVER['REDIRECT_URL'] ?>?page=<?= $paginator->get_prev_page() ?>"><?= $prev_page_label ?></a>
	<? endif ?>
	
	<? for ($i = $paginator->get_first_page(); $i <= $paginator->get_last_page(); $i++) : ?>
		<? if ($paginator->get_current_page() == $i) : ?>
			<b><?= $i ?></b>
		<? else : ?>
			<a href="<?= $_SERVER['REDIRECT_URL'] ?>?page=<?= $i ?>"><?= $i ?></a>
		<? endif ?>
	<? endfor ?>
	
	<? if ($paginator->has_next_page()) : ?>
		<a href="<?= $_SERVER['REDIRECT_URL'] ?>?page=<?= $paginator->get_next_page() ?>"><?= $next_page_label ?></a>
	<? endif ?>

<? endif ?>