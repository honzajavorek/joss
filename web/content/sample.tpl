<style type="text/css">
    .required { color: darkred; }
    th { text-align: right; }
</style>

<h1>NForm example 1</h1>

<?php
if ($form->getErrors()) {
    echo '<p>Opravte chyby:</p>';
    $form->renderErrors();
}
?>

<? $form->renderBegin(); ?>

<fieldset>
<legend>Testík</legend>
<table>
<tr class="required">
    <th><?=$form['name']->label?></th>
    <td><?=$form['name']->control?></td>
</tr>
</table>
</fieldset>

<div>
<?=$form['submit']->control?>
</div>

<? $form->renderEnd(); ?>
