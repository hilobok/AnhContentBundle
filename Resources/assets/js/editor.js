;(function ($, window, document, undefined) {
    var pluginName = "editor",
        plugin,
        defaults = {
            tags: [],
            toolbar: []
        };

    // The actual plugin constructor
    function Plugin(element, options) {
        plugin = this;

        this.element = element;
        this.options = $.extend({}, defaults, options);
        this.templates = {};

        this.readOnly = false;
        this.hasUnsavedUploads = false;
        this.hasChanges = false;

        this.textarea = null;
        this.codemirror = null;
        this.uploader = null;

        // this._defaults = defaults;
        // this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {
        init: function() {
            // create wrapper
            this.textarea = $(this.element).wrap('<div class="editor" />').get(0);

            this.initCodeMirror();
            this.buildLayout();
            this.buildToolbar();
            this.createEvents();
            this.initUploader();

            // recount chars
            CodeMirror.signal(this.codemirror, 'change', this.codemirror);
            this.hasChanges = false;

            $('.editor-uploader-list').empty();

            // add uploaded images from entity
            var assets = this.getAssets();
            for (i in assets) {
                this.addAsset(assets[i]);
            }
        },

        initCodeMirror: function() {
            this.codemirror = CodeMirror.fromTextArea(this.textarea, {
                mode: {
                    name: 'bbcode',
                    tags: this.options.tags
                },
                lineWrapping: true
            });
        },

        initUploader: function() {
            this.uploader = new qq.FineUploader({
                element: $('.editor-uploader-progress').get(0),
                button: $('.editor-upload-button').get(0),

                multiple: true,

                request: {
                    endpoint: plugin.options.uploader_endpoint
                },

                failedUploadTextDisplay: {
                        mode: 'custom'
                        // responseProperty: 'error'
                },

                callbacks: {
                    onComplete: function(id, fileName, response) {
                        $('.qq-upload-success').remove();

                        if (!response.success) {
                            return;
                        }

                        plugin.hasUnsavedUploads = true;

                        var assets = plugin.getAssets();
                        assets.push(response.asset);
                        plugin.setAssets(assets);

                        plugin.addAsset(response.asset);
                    }
                }
            });
        },

        buildLayout: function() {
            $('.editor').prepend('<div class="editor-toolbar" /><div class="editor-uploader" />');
            $('.CodeMirror').wrap('<div class="editor-codemirror" />');

            $('.editor-uploader')
                .append('<div class="editor-uploader-toolbar" />')
                .append('<div class="editor-uploader-progress" />')
                .append('<div class="editor-uploader-list" />')
            ;

            $('.editor-uploader-toolbar')
                .append('<span class="editor-upload-button button">Upload</span>')
            ;
        },

        buildToolbar: function() {
            var toolbar = $('.editor-toolbar');

            for(var i in this.options.toolbar) {
                var tag = this.options.toolbar[i];

                if (tag === '---') {
                    $('<span class="editor-toolbar-delimiter">|</span>')
                        .appendTo(toolbar)
                    ;

                    continue;
                }

                var tagName = tag.tag;

                // plugin.settings.tags[tagName] = tag;
                this.templates[tagName] = tag;

                if (tag.hidden) {
                    continue;
                }

                $('<span class="editor-tag-button">' + tag.text + '</span>')
                    .data('tagName', tagName)
                    .appendTo(toolbar)
                    .click(this.onClickToolbar)
                ;
            }

            // toggle bbcode tags button
            // $('<span class="editor-toggle-tags"><i class="fa fa-code" /></span>')
            //     .appendTo(toolbar)
            // ;

            // toggle uploader button
            $('<span class="editor-uploader-button"><i class="fa fa-upload" /></span>')
                .appendTo(toolbar)
            ;

            // chars counter
            $('<span class="editor-toolbar-delimiter">|</span><span class="editor-chars-counter">0</span>')
                .appendTo(toolbar)
            ;
        },

        onClickToolbar: function(event) {
            event.preventDefault();
            plugin.insertTag($(this).data('tagName'), {});
        },

        insertTag: function (tagName, values) {
            values = $.extend({}, values, {
                selection: this.codemirror.getSelection()
            });

            var render = this.renderTag(tagName, values);

            var start = this.codemirror.getCursor('start');
            var end = this.codemirror.getCursor('end');

            this.codemirror.replaceRange(render.text, start, end);

            // reposition cursor
            if (render.offset > -1) {
                var newStart = CodeMirror.Pos(start.line, start.ch + render.offset);
                this.codemirror.setCursor(newStart);
            }

            this.codemirror.focus();
        },

        renderTag: function(tagName, values) {
            var tag = this.templates[tagName];
            var text = tag.template;

            for(var name in values) {
                text = text.replace('{' + name + '}', values[name]);
            }

            var offset = text.indexOf('|');

            text = text.replace('|', '');

            return {
                text: text,
                offset: offset
            };
        },

        getAssets: function() {
            var input = $('.anh_content_editor_assets');

            return input.val().length ? $.parseJSON(input.val()) : [];
        },

        setAssets: function(assets) {
            var input = $('.anh_content_editor_assets');
            input.val(JSON.stringify(assets));
        },

        addAsset: function(asset) {
            var html = '<span class="original-file-name">' + asset.originalFileName + '</span>';

            if ($('.anh_content_editor_image').length > 0) {
                html += '<a class="editor-asset-star" href=""><i class="fa fa-star"></i></a>';
            }

            html += '<a class="editor-asset-delete" href=""><i class="fa fa-minus"></i></a>';

            if (this.assetIsImage(asset.fileName)) {
                html += '<a class="editor-asset-align-left" href=""><i class="fa fa-align-left"></i></a>';
                html += '<a class="editor-asset-align-center" href=""><i class="fa fa-align-center"></i></a>';
                html += '<a class="editor-asset-align-right" href=""><i class="fa fa-align-right"></i></a>';
                html += '<a class="editor-asset-align-none" href=""><i class="fa fa-align-justify"></i></a>';
            } else {
                html += '<a class="editor-asset-align-none single" href=""><i class="fa fa-align-justify"></i></a>';
            }

            html += '<a class="editor-asset-zoom" href="' + asset.url + '" target="_blank"><i class="fa fa-search-plus"></i></a>';

            var i = $('<img src="' + asset.thumb + '" draggable="true" />')
                .data('image', asset)
                .appendTo($('.editor-uploader-list'))
                .wrap('<span />')
                .after(html)
            ;

            if ($('.anh_content_editor_image').val() == asset.fileName) {
                $('a.editor-asset-star', $(i).closest('span')).addClass('selected');
            }
        },

        deleteAsset: function(asset) {
            var assets = this.getAssets();
            assets = assets.filter(function(v) { return v.fileName != asset.fileName; });
            this.setAssets(assets);

            // check if asset used as paper thumb
            var thumb = $('.anh_content_editor_image');

            if (thumb.val() == asset.fileName) {
                thumb.val('');
            }
        },

        findThumb: function(fileName) {
            return $('.editor-uploader-list img').filter(function() {
                var data = $(this).data('image');
                return data.fileName == fileName;
            });
        },

        assetIsImage: function(fileName) {
            var re = /(?:\.([^.]+))?$/;
            var extension = re.exec(fileName)[1];
            var validExtensions = /(jpg|jpeg|png|bmp|gif)$/i;

            return validExtensions.test(extension);
        },

        createEvents: function() {
            $('.editor-uploader-list').on('dragstart', 'img', function(event) {
                if (plugin.readOnly) {
                    return false;
                }

                var image = $(this).data('image');

                var render = plugin.renderTag('asset', {
                    asset: image.fileName,
                    align: 'center'
                });

                event.originalEvent.dataTransfer.effectAllowed = 'move';
                event.originalEvent.dataTransfer.setData('Text', render.text);
            });

            // toggle asset controls
            $('.editor-uploader-list').on('mouseenter mouseleave', 'span', function(event) {
                $('a', this).toggle();
            });

            // aligned asset click
            $('.editor-uploader-list').on('click', '[class^="editor-asset-align"]', function(event) {
                event.preventDefault();

                if (plugin.readOnly) {
                    return;
                }

                var span = $(this).closest('span');
                var image = $('img', span).data('image');

                var aligns = ['left', 'center', 'right', 'none'];

                for (var i in aligns) {
                    if ($(this).hasClass('editor-asset-align-' + aligns[i])) {
                        var title = plugin.assetIsImage(image.fileName) ? '' : image.originalFileName;
                        plugin.insertTag('asset', {
                            asset: image.fileName,
                            align: aligns[i],
                            title: title
                        });
                        break;
                    }
                }
            });

            // delete asset
            $('.editor-uploader-list').on('click', 'a.editor-asset-delete', function(event) {
                event.preventDefault();

                var span = $(this).closest('span');
                var image = $('img', span).data('image');

                plugin.deleteAsset(image);
                span.remove();

                plugin.hasChanges = true;
            });

            // mark asset as starred
            $('.editor-uploader-list').on('click', 'a.editor-asset-star', function(event) {
                event.preventDefault();

                var $this = $(this);
                var image = '';

                if (!$this.hasClass('selected')) {
                    var span = $this.closest('span');
                    image = $('img', span).data('image').fileName;
                }

                $('.editor-uploader-list .editor-asset-star.selected')
                    .not(this)
                    .removeClass('selected')
                ;

                $this.toggleClass('selected').show();

                $('.anh_content_editor_image').val(image);

                plugin.hasChanges = true;
            });

            // toggle bbcode tags button
            // $('.editor-toggle-tags').click(function(event) {
            //         event.preventDefault();
            //         plugin.readOnly = !plugin.readOnly;
            //         $('[class^="cm-tag-"]').toggle();
            //         plugin.codemirror.setOption('readOnly', plugin.readOnly);
            //         plugin.codemirror.focus();
            //     })
            // ;

            // toggle uploader button
            $('.editor-uploader-button').click(function(event) {
                    event.preventDefault();
                    // plugin.uploader.reset();
                    $('.qq-upload-list').empty();
                    $('.editor-uploader').slideToggle();
                })
            ;

            // count chars
            this.codemirror.on('change', function(cm) {
                var text = cm.getValue().replace(/\[(.*?)\]|\s/g, "");
                $('.editor-chars-counter').text(text.length);
                plugin.hasChanges = true;
            });

            // highlight asset
            this.codemirror.on('cursorActivity', function(cm) {
                var pos = cm.getCursor();
                var line = cm.getLine(pos.line);
                var chunks = line.split(/\[|\]/);
                var len = 0;

                for (var i = 0; i < chunks.length; i++) {
                    if (len > pos.ch) {
                        break;
                    }

                    len = len + chunks[i].length + 1;

                    if (len <= pos.ch) {
                        continue;
                    }

                    var match = chunks[i].match(/^asset="(.+?)"/);
                    if (match) {
                        $('.editor-uploader-list img').removeClass('highlighted');
                        var thumb = plugin.findThumb(match[1]);
                        $(thumb).addClass('highlighted');
                        return;
                    }
                }

                $('.editor-uploader-list img').removeClass('highlighted');
            });

            // warn about unsaved changes or uploads
            $(window).bind('beforeunload', function(event) {
                if (plugin.hasUnsavedUploads) {
                    return 'You have unsaved attachments. Are you sure?';
                }

                if (plugin.hasChanges) {
                    return 'You have unsaved changes. Are you sure?';
                }
            });

            // prevent from displaying warning about unsaved uploads on form submit
            $(plugin.element).closest('form').submit(function() {
                plugin.hasUnsavedUploads = false;
                plugin.hasChanges = false;
            });
        }
    };

    $[pluginName] = $.fn[pluginName] = function (options) {
        if (!(this instanceof $)) {
            $.extend(defaults, options);

            return defaults;
        }

        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };

    $(function() {
        $('.editor').each(function() {
            $(this).editor($(this).data('options'));
        });
    });
})(jQuery, window, document);
