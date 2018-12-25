$(document).on('click', '.array-input > button', itemAddClick);
$(document).on('click', '.array-input .item-remove', itemRemoveClick);
$(document).on('moving-column.moved', '.array-input-table tr', itemMove);

function itemAddClick(e)
{
    itemAdd($(this).prev());
};

function itemRemoveClick(e)
{
    e.preventDefault();
    $(this).closest('tr').remove();
};

function itemMove(e)
{
    $(this).parent().find('tr').each(function() {
        var $row = $(this);
        itemIndex($row, $row.index());
    });
};

function itemIndex($row, idx)
{
    $row.find('input, textarea, select').each(function() {
        this.name = this.name.replace(/\[(?:\d+)?\]/, '[' + idx + ']');
    });
};

function itemAdd($table)
{
    var $row = $($table.parent().data('arrayInputTemplate'));

    //index
    var idx = -1;
    $table.find('input, textarea, select').each(function() {
        var m = this.name.match(/.+\[(\d+)\]\[[^\]]+\]$/),
            i = m === null ? -1 : parseInt(m[1]);

        if (i > idx) {
            idx = i;
        };
    });
    idx++;

    //properties
    $row.removeClass('hidden');
    itemIndex($row, idx);

    $table.find('tbody').append($row);
};
