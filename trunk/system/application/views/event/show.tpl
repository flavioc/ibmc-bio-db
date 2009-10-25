{if $event}

{if $event.file}
{assign var=file value=$event.file}
  {if count($file) > 0}
  <ul>
    <li>Sequences: {$file.base_sequences} / {$file.base_sequences}</li>
    <li>Labels: {if $file.total_labels}{$file.total_labels}{else}---{/if} / {$file.base_sequences}</li>
  </ul>
  {/if}
{/if}

{if $event.generate_file}
{assign var=generate_file value=$event.generate_file}
  {if count($generate_file) > 0}
    <ul>
      <li>DNA sequences: {$generate_file.base_sequences} / {$generate_file.base_sequences}</li>
      <li>DNA labels: {if $generate_file.total_labels}{$generate_file.total_labels}{else}---{/if} / {$generate_file.base_sequences}</li>
      
      {assign var=generated_file value=$event.generated_file}
      {if count($generated_file) > 0}
      <li>Protein sequences: {$generated_file.base_sequences} / {$generate_file.base_sequences}</li>
      {/if}
    </ul>
  {/if}
{/if}

{if $event.file1}
{assign var=file1 value=$event.file1}
{assign var=file2 value=$event.file2}
  {if count($file1) > 0}
  <ul>
    <li>DNA sequences: {$file1.base_sequences} / {$file1.base_sequences}</li>
    <li>DNA labels: {if $file1.total_labels}{$file1.total_labels}{else}---{/if} / {$file1.base_sequences}</li>
    {if count($file2) > 0}
      <li>Protein sequences: {$file2.base_sequences} / {$file1.base_sequences}</li>
      <li>Protein labels: {if $file2.total_labels}{$file2.total_labels}{else}---{/if} / {$file1.base_sequences}</li>
    {/if}
  </ul>
  {/if}
{/if}

{/if}