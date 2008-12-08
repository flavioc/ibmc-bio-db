{include file=header.tpl}

<h2>{$subtitle}</h2>

<script>
{literal}
$(document).ready(function () {

  var base_site = "{/literal}{site}{literal}/taxonomy/";

  function when_submit()
  {
    var rank = $('#rank').val();
    var name = $('#name').val();

    var url = base_site + 'search/';
    var url_total = base_site + 'search_total/';
    var params = {name: name, rank: rank};

    show_load();

    $.post(url_total, params, function(data) {
      $('#total_results').text(data);
    });

    $.post(url, params,
      function(data) {
        hide_load();

        $('#table_data').empty().appendDom([
          {
            tagName: 'table',
            id: 'table',
            class: 'data',
            style: 'display: none;',
            childNodes: [
              {
                tagName: 'tbody',
                childNodes: [
                  {
                    tagName: 'tr',
                    childNodes: [
                      {
                        tagName: 'th',
                        innerHTML: 'Name',
                      },
                      {
                        tagName: 'th',
                        innerHTML: 'Rank'
                      },
                      {
                        tagName: 'th',
                        innerHTML: 'Parent'
                      },
                      {
                        tagName: 'th',
                        class: 'deletable_column',
                        innerHTML: 'Delete'
                      }
                    ]
                  }
                ]
              }
            ]
          }
          ]
        );

        var rows = $.evalJSON(data);

        for (var i=0; i< rows.length; i++) {
          var row = rows[i];

          if(row.parent_name == null) {
            row.parent_name = '-';
          }

          {/literal}
          {if $child_id}
          var link = base_site + 'set_parent/{$child_id}/' + row.id;
          {else}
          var link = base_site + 'view/' + row.id;
            {/if}
          {literal}

          $('#table').appendDom([
            {
              tagName: 'tr',
              id: build_tr_id(row.id),
              childNodes: [
                {
                  tagName: 'td',
                  innerHTML: '<a href="' + link + '">' + row.name + '</a>'
                },
                {
                  tagName: 'td',
                  innerHTML: row.rank_name
                },
                {
                  tagName: 'td',
                  innerHTML: row.parent_name
                },
                {
                  tagName: 'td',
                  class: 'deletable_column',
                  childNodes: [
                    {
                      tagName: 'a',
                      class: 'deletable',
                      href: '#',
                      id: build_delete_id('tax', row.id),
                      innerHTML: 'Delete'
                    }
                  ]
                }
              ]
            }
          ]);
        }

        $('#table').fadeIn();

        $('.deletable').click(function() {
          var id = parse_id($(this).attr('id'));
          var url = base_site + 'delete/' + id;

          $.post(url, function(data) {
            if(is_ok(data)) {
              var tr_id = build_tr_id(id);

              $('#' + tr_id).fadeOut("slow");
            } else {
              alert("Error deleting name: " + data);
            }
          });
        }).confirm();
      }
    );
  }

  $("#form_search").validate({
    rules: {
      name: {
        required: true,
        minlength: 2,
        maxlength: 512
      },
      rank: {
        required: true
      }
    },
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });
});
{/literal}
</script>

<p>
{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_row type=select data=$ranks name=rank msg='Rank:' blank=yes}
{form_submit name=submit_search msg=Search}
{form_end}
</p>

<h3>Results (<span id="total_results">-</span>)</h3>

{loader_pic}

<p>
<div id="table_data">
  
</div>
</p>

{if $child_id}
<p>Search for the parent's taxonomy name and then click on the name to apply changes</p>
{/if}

{include file=footer.tpl}
