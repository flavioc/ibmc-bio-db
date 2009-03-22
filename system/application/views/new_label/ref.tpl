
<h2>New reference label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_ref_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide="#show_sequences, #data_area, #sequence_help"}

<span id="data_area">
{form_row readonly=readonly name=ref msg='Sequence:'}
{form_hidden name=hidden_ref}
</span>

<p id="sequence_help">Please select a sequence below:</p>
<div id="show_sequences"></div>

</fieldset>

{form_submit name=submit_file msg='Add label'}
{form_end}

{include file="common_label/ref.tpl" form=form_add_label}

