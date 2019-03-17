let UI = {
    //加载系统弹窗
    //obj:对象，包括{title:标题，msg：显示的消息，img：显示的图标（ok,error,warning）}
    alert: function (obj) {
        //定义形参obj的属性
        let title = (obj == undefined || obj.title == undefined) ? '系统消息' : obj.title;
        let img = (obj == undefined || obj.img == undefined) ? 'warning' : obj.img;
        let msg = (obj== undefined || obj.msg == undefined) ? '' : obj.msg;
        // let url = (obj== undefined || obj.url == undefined) ? '' : obj.url;
        //.replace('{}',变量)
        let html = this.getAlertHtml().replace('{title}',title).replace('{msg}',msg).replace('{img}',img);
        //插入html到body
        $(html).appendTo('body');
        $('#UI_modal_sm').modal({backdrop: 'static'});
        $('#UI_modal_sm').modal('show');//显示模态框
        //.modal('hide')触发的事件
        $('#UI_modal_sm').on('hidden.bs.modal', function (e) {
            $('#UI_modal_sm').remove();
        })
    },
    //加载页面
    // obj：对象，包含{title:标题,url:加载的页面url,width:宽度,height:高度}
    open: function(obj) {
        let title = (obj == undefined || obj.title==undefined) ? '' : obj.title;
        let width = (obj == undefined || obj.width==undefined) ? 500 : obj.width;
        let height = (obj == undefined || obj.height==undefined) ? 450 : obj.height;
        // let url = (obj == undefined || obj.url==undefined) ? '' : obj.url;
        let html = this.getModalHtml().replace('{title}', title);

        //插入html到body
        $('body').append(html);
        $('#UI_modal_lg .modal-lg').css('width',width); //设置模态框宽度
        $('#UI_modal_lg .modal-body').css('height',height);//设置模态框高度
        //给iframe设置url
        $('#UI_modal_lg iframe').attr('src', obj.url);

        $('#UI_modal_lg').modal({backdrop: 'static'});
        $('#UI_modal_lg').modal('show');//显示模态框
        //.modal('hide')影藏触发的事件
        $('#UI_modal_lg').on('hidden.bs.modal', function (e) {
            $('#UI_modal_lg').remove();
        })
    },
    getAlertHtml: function () {
        //定义要插入的html
        let alert_html = '<div class="modal fade" tabindex="-1" id="UI_modal_sm">\n' +
            '    <div class="modal-dialog modal-sm" role="document">\n' +
            '        <div class="modal-content">\n' +
            '            <div class="modal-header">\n' +
            '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '                <h4 class="modal-title">{title}</h4>\n' +
            '            </div>\n' +
            '            <div class="modal-body">\n' +
            '                <p><img src="/static/image/{img}.png">{msg}</p>\n' +
            '            </div>\n' +
            '            <div class="modal-footer">\n' +
            '                <button id="submit" type="button" class="btn btn-primary" onclick="$(\'#UI_modal_sm\').modal(\'hide\')">确定</button>\n' +
            '            </div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '</div>';
        return alert_html;
    },
    getModalHtml: function () {
        let html = '<div class="modal fade my-modal-lg" id="UI_modal_lg" tabindex="-1" style="display: none;">\n' +
            '    <div class="modal-dialog modal-lg" role="document">\n' +
            '      <div class="modal-content">\n' +
            '\n' +
            '        <div class="modal-header">\n' +
            '          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>\n' +
            '          <h4 class="modal-title" id="myLargeModalLabel">{title}</h4>\n' +
            '        </div>\n' +
            '        <div class="modal-body">\n' +
            '           <iframe frameborder="0" scrolling="auto" style="width:100%;height:100%"></iframe>\n' +
            '        </div>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>';
        return html;
    }
};