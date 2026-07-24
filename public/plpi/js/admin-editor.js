document.addEventListener('DOMContentLoaded', function () {
    const richSelector = 'textarea[data-editor="rich"], textarea.admin-rich-editor';
    if (!document.querySelector(richSelector)) return;

    function tinymceUrl() {
        const current = document.currentScript && document.currentScript.src ? document.currentScript.src : '';
        if (current) {
            try {
                const url = new URL(current, window.location.href);
                if (url.pathname.indexOf('/plpi/js/admin-editor.js') !== -1) {
                    url.pathname = url.pathname.replace('/plpi/js/admin-editor.js', '/plpi/vendor/tinymce/tinymce.min.js');
                    url.search = '';
                    return url.toString();
                }
            } catch (error) {
                // Fall through to the public path below.
            }
        }

        return new URL('/plpi/vendor/tinymce/tinymce.min.js', window.location.origin).toString();
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
        script.addEventListener('error', function () {
            document.querySelectorAll(richSelector).forEach(function (textarea) {
                textarea.style.display = '';
            });
        }, { once: true });
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
        extended_valid_elements: 'iframe[src|title|width|height|allow|allowfullscreen|frameborder|loading|referrerpolicy]',
        valid_children: '+body[iframe]',
        media_live_embeds: true,
        entity_encoding: 'raw',
        content_style: [
            'html,body{font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65;color:#233348;padding:12px;}',
            'p{margin:0 0 10px;}',
            'blockquote{margin:16px 0;padding:16px 18px;border-left:5px solid #0f766e;border-radius:0 14px 14px 0;background:#ecfdf5;color:#0b2b4c;font-size:16px;line-height:1.7;}',
            'blockquote p{margin:0 0 8px;}',
            'blockquote p:last-child{margin-bottom:0;}',
            'a{color:#0f766e;}',
            'table{border-collapse:collapse;width:100%;}',
            'td,th{border:1px solid #dbe5ef;padding:8px;}',
            'iframe{display:block;width:100%;max-width:760px;aspect-ratio:16/9;height:auto;margin:18px 0;border:0;border-radius:14px;}'
        ].join(''),
        skin: 'oxide',
        content_css: false,
        setup: function (editor) {
            editor.on('init', function () {
                const body = editor.getBody();
                if (!body) return;
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
