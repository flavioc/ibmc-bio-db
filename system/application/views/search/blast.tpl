<h2>BLAST search</h2>

{form_open to='blast/do_blast' name=add_form}

<fieldset id="basic_fieldset">
{form_hidden name=encoded_tree value=$tree_json}
{form_hidden name=transform value=$transform}
{form_row name=identifier msg='Search identifier (*):'}
{form_row type=textarea name=query_sequences msg='Query sequences (FASTA):' cols=70 rows=5}
{form_row type=select data=$blast_programs name=blast_program msg='Program:' key=id value=id}
{form_row type=select data=$expect_values name=expect_value msg='Expect:' key=id start=4}
{form_row type=checkbox name=generate_labels msg='Generate labels:'}
</fieldset>

<div id="show_advanced"></div>

<fieldset id="advanced_fieldset">
<p>The query sequence is NOT filtered for low complexity regions by default.</p>
{form_row type=checkbox name=low_complexity msg='Low complexity:'}
{form_row type=checkbox name=mask_lookup msg='Mask for lookup table only:'}
{form_row type=select data=$matrix name=matrix msg='Matrix' key=id start=4}
{form_row type=checkbox name=ungapped_alignment msg='Perform ungapped alignment:'}
{form_row type=select data=$query_genetic_codes name=query_genetic_code msg='Query Genetic Codes (blastx only):'}
{form_row type=select data=$db_genetic_codes name=db_genetic_code msg='Database Genetic Codes (tblast[nx] only):'}
{form_row name=advanced_options msg='Other advanced options:'}
</fieldset>

{form_submit name="submit" msg="BLAST"}
{form_end}

{include file=search/operation_sequences.tpl dom_id=input_list}

{literal}
<style>
#advanced_fieldset .desc {
  width: 320px;
}
#show_advanced {
  margin-top: 5px;
  margin-bottom: 5px;
}
</style>

<script>
$(function () {
  $('#show_advanced').minusPlus({
    enableImage: true,
    plusEnabled: function () {
      $('#advanced_fieldset').show();
    },
    minusEnabled: function () {
      $('#advanced_fieldset').hide();
    },
    enabled: false,
    plusText: 'Show advanced pane',
    minusText: 'Hide advanced pane'
  });
  $('#advanced_fieldset').hide();
});
</script>
{/literal}