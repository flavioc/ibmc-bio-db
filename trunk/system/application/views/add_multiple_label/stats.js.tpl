$('#info_results').show().html("<ul>\
<li>New labels: {$count_new}</li>\
<li>Regenerated: {$count_regenerate}</li>\
<li>New multiple: {$count_new_multiple}</li>\
<li>New generated: {$count_new_generated}</li>\
<li>Updated: {$count_updated}</li>\
<li>New multiple generated: {$count_new_multiple_generated}</li>\
<li>Invalid: {$count_invalid}</li>\
</ul>").effect("highlight", {literal}{}{/literal}, 3000);