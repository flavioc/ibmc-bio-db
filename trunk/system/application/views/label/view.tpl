<h2>View label</h2>

<div class="data_show">
  <p><span class="desc">Name: </span><span id="labelname">{$label.name}</span></p>
  <p><span class="desc">Type: </span><span id="labeltype">{$label.type}</span></p>

  <p><span class="desc">Auto Add: </span>
    <span id="labelautoadd">{boolean value=$label.autoadd}</span>
  </p>

  <p><span class="desc">Must Exist: </span>
    <span id="labelmustexist">{boolean value=$label.must_exist}</span>
  </p>

  <p><span class="desc">Generate on creation: </span>
    <span id="labelauto_on_creation">{boolean value=$label.auto_on_creation}</span>
  </p>

  <p><span class="desc">Generate on modification: </span>
    <span id="labelauto_on_modification">{boolean value=$label.auto_on_modification}</span>
  </p>
  <br />

  <p><span class="desc">Deletable: </span>
    <span id="labeldeletable">{boolean value=$label.deletable}</span>
  </p>

  <p><span class="desc">Editable: </span>
    <span id="labeleditable">{boolean value=$label.editable}</span>
  </p>

  <p><span class="desc">Multiple: </span>
    <span id="labelmultiple">{boolean value=$label.multiple}</span>
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
<ul>
<li><a href="{site}/label/edit/{$label.id}">Edit</a></li>
<li><a href="{site}/label/browse">List Labels</a></li>
</ul>
</p>

