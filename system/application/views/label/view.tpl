<h2>View label</h2>

<div class="data_show">
  <p><span class="desc">Name: </span><span id="labelname">{$label.name}</span></p>
  <p><span class="desc">Type: </span><span id="labeltype">{$label.type}</span></p>

  <p><span class="desc">Auto Add: </span>
    <span id="labelautoadd">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {boolean value=$label.autoadd}
    {/if}
    </span>
  </p>

  <p><span class="desc">Must Exist: </span>
    <span id="labelmustexist">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {boolean value=$label.must_exist}
    {/if}
    </span>
  </p>

  <p><span class="desc">Generate on creation: </span>
    <span id="labelauto_on_creation">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {boolean value=$label.auto_on_creation}
    {/if}
    </span>
  </p>

  <p><span class="desc">Generate on modification: </span>
    <span id="labelauto_on_modification">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {boolean value=$label.auto_on_modification}
    {/if}
    </span>
  </p>
  <br />

  <p><span class="desc">Deletable: </span>
    <span id="labeldeletable">
    {if $label.default}
      {boolean value=!$label.default}
    {else}
      {boolean value=$label.deletable}
    {/if}
    </span>
  </p>

  <p><span class="desc">Is default: </span><span id="labeldefault">{boolean value=$label.default}</span></p>

  <p><span class="desc">Code: </span>
    <span class="code" id="labelcode">{if $label.code}{$label.code}{else}---{/if}</span>
  </p>

  <p><span class="desc">Comment: </span>
     <span class="comment" id="labelcomment">{if $label.comment}{$label.comment}{else}---{/if}</span>
  </p>
</div>


<p>
{if !$label.default}
<a href="{site}/label/edit/{$label.id}">Edit</a>
{/if}
</p>

