<?php


require __DIR__ . '/../src/GenDoc.php';

use Wqy\GenDoc;

$gen = new GenDoc();

echo '<!-- gendoc {{{ -->', "\n\n\n";

$gen->handleHtml();

echo "\n\n\n", '<!-- gendoc }}} -->';

?>


<script src="//cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>

<link href="//cdn.bootcss.com/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
<script src="//cdn.bootcss.com/jqueryui/1.12.1/jquery-ui.min.js"></script>

<div style="position: fixed; right: 0; top: 0;">
<button id="to-code">to code</button>
</div>
<div id="dlg" style="display: none; position: fixed; bottom: 0; ">
<textarea id="ta" cols="80" rows="30"></textarea>
</div>

<script>

var currentTable;

$('table').on('click', function () {
    currentTable = this;
    $('table').css('background', 'none');
    $(this).css('background', 'pink');
});

function maxLength($tr)
{
    var len = 0;
    $tr.each(function () {
        var l = $(this).find('td:eq(0)').text().length;
        if (l > len) {
            len = l;
        }
    });

    return len;
}

function textArray($tr, len)
{
    var codes = [];
    $tr.each(function () {
        var t = $(this).find('td:eq(0)').text().trim();
        var pad = '';
        if (t.length < len) {
            pad = ' '.repeat(len - t.length);
        }

        var cmt = $(this).find('td:gt(0)').text().replace(/\s+/g, ' ');
        codes.push({text: t, pad: pad, cmt: cmt});
    });
    return codes;
}

$('#to-code').on('click', function () {
    var $tr = $(currentTable).find('tbody tr');
    var ml = maxLength($tr);

    var codes = textArray($tr, ml);

    console.log(codes);

    var codeText = '';
    codes.forEach(function (v) {
        codeText += "$key = '" + v.text + "'; " + v.pad + "// " + v.cmt + "\n";
        codeText += "$rs['" + v.text + "'" + v.pad + "] = isset($data[$key]) ? $data[$key] : null;\n";
    });
    $('#ta').val(codeText);
    $('#dlg').dialog({width: 600, height: 400});
});



</script>
