<h2>BLAST results</h2>

{if $empty}

<p>None of the sequences matched the specified query.</p>

{else}

{form_open to='blast/add_labels' name=add_form}
<p>If you want to annotate the sequences with BLAST information, click below:</p>
{form_hidden name=labels value=$labels}
{form_submit name="submit" msg="Save labels"}
{form_end}

{/if}

<br />
<hr />

<pre>
{$output}
</pre>