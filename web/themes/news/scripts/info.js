$(document).ready(function () {

// прокрутка колонок в таблице   
    $('.table-container div').on('click', function() {
        var current_column = 0,
            column_num = 0,
            next_column = 0,
            current_table,
            btn;
        
        btn = $(this).attr('class'); // нажато далее или назад
        
        current_table = $(this).parent().find('table'); // текущая таблица
        current_column = current_table.find('.table-header').last().find('.visible-cell').index(); // текущая колонка
        column_num = current_table.find('.table-header td').length - 1; // всего колонок
        
        // какой столбей показать
        if (btn == 'columnn-next') {
            next_column  = current_column;

            if(next_column > column_num - 1){
                next_column = 0;
            }
        } else {
            next_column  = current_column - 2;
            if(next_column == -1){
                next_column = column_num - 1;
            }   
            alert(next_column);
        }       
        
        current_table.find('tr').each(function () {
            $(this).find('.visible-cell').removeClass('visible-cell');
            $(this).find('.parameter-val:eq(' + next_column + ')').addClass('visible-cell');
        });
        
    });
});    