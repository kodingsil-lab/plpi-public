document.addEventListener('DOMContentLoaded', function () {
    const richSelector = 'textarea[data-editor="rich"], textarea.admin-rich-editor';
    if (!document.querySelector(richSelector)) return;

    function tinymceUrl() {
        const current = document.currentScript && document.currentScript.src ? document.currentScript.src : '';
        if (current.indexOf('/plpi/js/admin-editor.js') !== -1) {
            return current.replace('/plpi/js/admin-editor.js', '/plpi/vendor/tinymce/tinymce.min.js');
        }

        return '/plpi-public/public/plpi/vendor/tinymce/tinymce.min.js';
    }

    function loadTinyMce(callback) {
        if (window.tinymce) {
            callback();
            return;
        }

        const existing = document.querySelector('script[data-tinymce-loader]');
        if (existing) {
            existing.addEventListener('load', callback, { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = tinymceUrl();
        script.defer = true;
        script.dataset.tinymceLoader = '1';
        script.addEventListener('load', callback, { once: true });
        document.head.appendChild(script);
    }

    function initTinyMce() {
        if (!window.tinymce) return;

        window.tinymce.init({
        selector: richSelector,
        license_key: 'gpl',
        promotion: false,
        branding: false,
        menubar: 'file edit view insert format tools table help',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: [
            'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor',
            'alignleft aligncenter alignright alignjustify | blockquote bullist numlist outdent indent | link image media table',
            'removeformat | searchreplace visualblocks code fullscreen preview help'
        ].join(' | '),
        toolbar_mode: 'sliding',
        font_family_formats: 'Arial=Arial,Helvetica,sans-serif;Times New Roman=Times New Roman,Times,serif;Verdana=Verdana,Geneva,sans-serif;Tahoma=Tahoma,Geneva,sans-serif;Courier New=Courier New,Courier,monospace',
        height: 320,
        min_height: 220,
        resize: true,
        statusbar: true,
        convert_urls: false,
        relative_urls: false,
        remove_script_host: false,
        entity_encoding: 'raw',
        content_style: [
            'html,body{font-family:Arial,Helvetica,sans-serif!important;font-size:14px;font-weight:400!important;line-height:1.65;color:#233348;padding:12px;}',
            'body.mce-content-body,body.mce-content-body *{font-family:Arial,Helvetica,sans-serif!important;font-weight:400!important;}',
            'p{margin:0 0 10px;font-weight:400!important;}',
            'p,div,span,li,td,th,b,strong{font-weight:400!important;}',
            'blockquote{margin:16px 0;padding:16px 18px;border-left:5px solid #0f766e;border-radius:0 14px 14px 0;background:#ecfdf5;color:#0b2b4c;font-size:16px;line-height:1.7;}',
            'blockquote p{margin:0 0 8px;}',
            'blockquote p:last-child{margin-bottom:0;}',
            'a{color:#0f766e;}',
            'table{border-collapse:collapse;width:100%;}',
            'td,th{border:1px solid #dbe5ef;padding:8px;}'
        ].join(''),
        skin: 'oxide',
        content_css: false,
        setup: function (editor) {
            editor.on('init', function () {
                const body = editor.getBody();
                if (!body) return;

                let content = editor.getContent();
                content = content
                    .replace(/<\/?(strong|b)\b[^>]*>/gi, '')
                    .replace(/font-weight\s*:\s*(bold|bolder|[5-9]00)\s*;?/gi, '');
                editor.setContent(content);
                body.style.fontWeight = '400';
                body.style.fontFamily = 'Arial, Helvetica, sans-serif';

                body.querySelectorAll('b,strong').forEach(function (node) {
                    const span = editor.getDoc().createElement('span');
                    span.innerHTML = node.innerHTML;
                    node.parentNode.replaceChild(span, node);
                });
                body.querySelectorAll('[style]').forEach(function (node) {
                    node.style.fontFamily = '';
                    node.style.fontWeight = '';
                    if (!node.getAttribute('style')) {
                        node.removeAttribute('style');
                    }
                });
                editor.save();
            });
            editor.on('change keyup undo redo', function () {
                editor.save();
            });
        }
        });
    }

    loadTinyMce(initTinyMce);
});
