
<h2>Edit reference label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_ref_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide="#show_sequences, #data_area, #sequence_help"}

<span id="data_area">
{form_row readonly=readonly name=ref msg='Sequence:'}
{form_hidden name=hidden_ref}
</span>

<p id="sequence_help">Please select a sequence below:</p>
<div id="show_sequences"></div>

</fieldset>

{form_submit name=submit_file msg='Edit label'}
{form_end}

{include file="common_label/ref.tpl" form=form_edit_label}

