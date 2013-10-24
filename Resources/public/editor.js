;(function($) {
    $.editor = function(options) {

        var defaults = {
            textarea: {},       // textarea to attach editor (required)
            tagList: {},        // tags definitions (required)
            tagSet: {},         // toolbar definition

            tags: {},           // tags from tagSet, builded

            uploader: {
                endpoint: '/some/path'  // uploader endpoint url (required)
            },

            path: {
                uploads: '',     // web path to uploaded images dir (required)
                thumbs: ''      // web path to thumbs dir (required)
            }

            // propertyName: 'value',
            // onSomeEvent: function() {}
        };

        // private
        var plugin = this;
        var codemirror = {};
        var uploader = {};

        var haveUnsavedUploads = false;
        var readOnly = false;

        // public
        plugin.settings = {};

        var init = function() {
            plugin.settings = $.extend({}, defaults, options);

            // define mode
            CodeMirror.defineMode('bbcode', codemirrorBbcodeMode);

            // create wrapper
            var textarea = $(plugin.settings.textarea).wrap('<div class="editor" />').get(0);

            // init codemirror
            codemirror = CodeMirror.fromTextArea(textarea, {
                mode: 'bbcode',
                lineWrapping: true
            });

            // build layout
            $('.editor').prepend('<div class="editor-toolbar" /><div class="editor-uploader" />');
            $('.CodeMirror').wrap('<div class="editor-codemirror" />');

            $('.editor-uploader')
                .append('<div class="editor-uploader-toolbar" />')
                .append('<div class="editor-uploader-progress" />')
                .append('<div class="editor-uploader-list" />')
            ;

            $('.editor-uploader-toolbar')
                .append('<span class="editor-upload-button">Uload</span>')
            ;

            // events
            $('.editor-uploader-list').on('dragstart', 'img', function(event) {
                if (readOnly) {
                    return false;
                }

                var image = $(this).data('image');

                var render = renderTag('asset', {
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

                if (readOnly) {
                    return;
                }

                var span = $(this).closest('span');
                var image = $('img', span).data('image');

                var aligns = ['left', 'center', 'right'];

                for (var i in aligns) {
                    if ($(this).hasClass('editor-asset-align-' + aligns[i])) {
                        insertAssetTag(image, { align: aligns[i] });
                        break;
                    }
                }
            });

            // delete asset
            $('.editor-uploader-list').on('click', 'a.editor-asset-delete', function(event) {
                event.preventDefault();

                var span = $(this).closest('span');
                var image = $('img', span).data('image');

                delete_image(image);

                span.remove();
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

                $('#anh_content_form_type_paper_image').val(image);
            });

            // build toolbar
            // tags buttons
            for(var i in plugin.settings.tagSet) {
                var tag = plugin.settings.tagSet[i];

                if (tag === '---') {
                    $('<span class="editor-toolbar-delimiter">|</span>')
                        .appendTo($('.editor-toolbar'))
                    ;

                    continue;
                }

                var tagName = tag.tag;

                plugin.settings.tags[tagName] = tag;

                if (tag.hidden) {
                    continue;
                }

                $('<span class="editor-tag-button">' + tag.text + '</span>')
                    .data('tagName', tagName)
                    .appendTo($('.editor-toolbar'))
                    .click(tag_button_click)
                ;
            }

            // toggle bbcode tags button
            $('<span class="editor-toggle-tags"><i class="fa fa-code" /></span>')
                .appendTo($('.editor-toolbar'))
                .click(function(event) {
                    event.preventDefault();
                    readOnly = !readOnly;
                    $('[class^="cm-tag-"]').toggle();
                    codemirror.setOption('readOnly', readOnly);
                    codemirror.focus();
                })
            ;

            // toggle uploader button
            $('<span class="editor-uploader-button"><i class="fa fa-upload" /></span>')
                .appendTo($('.editor-toolbar'))
                .click(function(event) {
                    event.preventDefault();

                    $('.qq-upload-list').empty();
                    $('.editor-uploader').slideToggle();
                })
            ;

            // chars counter
            $('<span class="editor-toolbar-delimiter">|</span><span class="editor-chars-counter">0</span>')
                .appendTo($('.editor-toolbar'))
            ;

            codemirror.on('change', function(cm) {
                var text = cm.getValue().replace(/\[(.*?)\]|\s/g, "");
                $('.editor-chars-counter').text(text.length);
            });

            CodeMirror.signal(codemirror, 'change', codemirror);

            // warn about unsaved uploads
            $(window).bind('beforeunload', function(event) {
                if (haveUnsavedUploads) {
                    return 'You have unsaved attachments. Are you sure?';
                }
            });

            // prevent from displaying warning about unsaved uploads on form submit
            $(plugin.settings.textarea).closest('form').submit(function() {
                haveUnsavedUploads = false;
            });

            // init uploader
            uploader = new qq.FineUploader({
                element: $('.editor-uploader-progress')[0],

                button: $('.editor-upload-button')[0],

                request: {
                    endpoint: plugin.settings.uploader.endpoint
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

                        haveUnsavedUploads = true;

                        var image = {
                            fileName: response.fileName,
                            originalFileName: fileName,
                            size: response.size
                        };

                        var images = getAssets();

                        images.push(image);

                        setAssets(images);
                        add_image(image);
                    }
                }
            });

            // add uploaded images from entity
            var images = getAssets();

            $('.editor-uploader-list').empty();

            for (i in images) {
                add_image(images[i]);
            }
        };

        // plugin.foo_public_method = function() {
        //     // code goes here
        // };

        // get already uploaded files
        var getAssets = function() {
            var input = $('#anh_content_form_type_paper_assets');

            return input.val().length ? $.parseJSON(input.val()) : [];
        };

        var setAssets = function(images) {
            var input = $('#anh_content_form_type_paper_assets');
            input.val(JSON.stringify(images));
        };

        var add_image = function(image) {
            var thumb = plugin.settings.path.thumbs + image.fileName;
            var upload = plugin.settings.path.uploads + image.fileName;

            var i = $('<img src="' + thumb + '" draggable="true" />')
                .data('image', image)
                .appendTo($('.editor-uploader-list'))
                .wrap('<span />')
                .after('<a class="editor-asset-star" href=""><i class="fa fa-star"></i></a>')
                .after('<a class="editor-asset-delete" href=""><i class="fa fa-minus"></a>')
                .after('<a class="editor-asset-align-left" href=""><i class="fa fa-align-left"></i></a>')
                .after('<a class="editor-asset-align-center" href=""><i class="fa fa-align-center"></i></a>')
                .after('<a class="editor-asset-align-right" href=""><i class="fa fa-align-right"></i></a>')
                .after('<a class="editor-asset-zoom" href="' + upload + '" target="_blank"><i class="fa fa-search-plus"></i></a>')

            ;

            if ($('#anh_content_form_type_paper_image').val() == image.fileName) {
                $('a.editor-asset-star', $(i).closest('span')).addClass('selected');
            }
        };

        var delete_image = function(image) {
            var images = getAssets();
            images = images.filter(function(v) { return v.fileName != image.fileName; });
            setAssets(images);

            // check if image used as paper thumb
            var thumb = $('#anh_content_form_type_paper_image');

            if (thumb.val() == image.fileName) {
                thumb.val('');
            }
        };

        // button click
        var tag_button_click = function(event) {
            event.preventDefault();

            insertTag($(this).data('tagName'), {});
        };

        var insertTag = function (tagName, values) {
            values = $.extend({}, values, {
                selection: codemirror.getSelection()
            });

            var render = renderTag(tagName, values);

            var start = codemirror.getCursor('start');
            var end = codemirror.getCursor('end');

            codemirror.replaceRange(render.text, start, end);

            // reposition cursor
            if (render.offset > -1) {
                var newStart = CodeMirror.Pos(start.line, start.ch + render.offset);
                codemirror.setCursor(newStart);
            }

            codemirror.focus();
        };

        var insertAssetTag = function(image, values) {
            insertTag('asset', {
                asset: image.fileName,
                align: values.align
            });
        };

        var renderTag = function(tagName, values) {
            var tag = plugin.settings.tags[tagName];
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
        };

        var codemirrorBbcodeMode = function(config, options) {
            return {
                startState: function() {
                    return {
                        inTag: false,
                        style: false
                    };
                },

                token: function(stream, state) {
                    if(!state.inTag && stream.peek() == '[') {
                        stream.next();
                        state.inTag = true;
                        state.style = 'unknown';
                    }

                    if(state.inTag) {
                        if(stream.peek() == '/') {
                            stream.next();
                        }

                        for(var i in plugin.settings.tagList) {
                            var tagName = plugin.settings.tagList[i];
                            var pattern = new RegExp(tagName + "(\\W|$)");

                            var match = stream.match(pattern);

                            if(match) {
                                state.style = 'tag-' + tagName;

                                stream.backUp(match[1].length);
                                break;
                            }
                        }

                        if(stream.skipTo(']')) {
                            stream.next();
                            state.inTag = false;
                        } else {
                            stream.skipToEnd();
                        }

                        return state.style;
                    } else {
                        var _linter = stream.skipTo('[') || stream.skipToEnd();

                        return null;
                    }
                }
            };
        };

        init();
    };
})(jQuery);