class NewsBlocksEditor {

    constructor(el) {
        if (el.length) {

            this.el = el;
            this.wndNewsBlockEdit = $('#wndNewsBlockEdit');
            this.content = this.wndNewsBlockEdit.find('*[data-role="content"]');
            this.form = this.wndNewsBlockEdit.find('form');



            console.log('NewsBlocksEditor');

            $('body').on('click', '*[data-role="news_block"]', (e) => {
                let block_id = $(e.currentTarget).attr('data-block_id');

                $.ajax({
                    type: "POST",
                    url: "/admin/news/news/block-editor-detail",
                    data: {block_id: block_id},
                    success: (data) => {
                        if (data.success) {
                            let html = '';
                            html += `<input type="hidden" name="NewsBlock[id]" value="${data.item.id}">`;
                            html += data.item.type_block;
                            html += `<input style="width: 100%;" type="text" name="NewsBlock[name]" value="${data.item.name}">`;
                            html += `<textarea name="NewsBlock[desc]" class="editor">${data.item.desc}</textarea>`;
                            this.content.html(html);

                            $.fancybox.open({src: '#wndNewsBlockEdit'});

                            $('textarea.editor').ckeditor();
                        } else {
                            alert(data.message);
                        }

                    }
                });

            });

            $('body').on('click', '*[data-role="btn_save"]', (e) => {

                $.ajax({
                    type: "POST",
                    url: "/admin/news/news/block-editor-save",
                    data: this.form.serialize(),
                    success: (data) => {
                        if (data.success) {
                            $.fancybox.close();

                            $.ajax({
                                type: "GET",
                                url: window.location.pathname,
                                success: (data) => {
                                    $('#content').find('*[data-role="NewsBlocksEditor"]').
                                    replaceWith($(data).find('*[data-role="NewsBlocksEditor"]'));

                                }
                            });
                        }
                    }
                });
            });


        }
    }


}


let _NewsBlocksEditor;
$(function () {
    _NewsBlocksEditor = new NewsBlocksEditor($('*[data-role="NewsBlocksEditor"]'));
});