;(function() {
    var options = $.editor();

    var tags = (options.tags || []).concat([
        // block filter
        'align',
        'left',
        'right',
        'center',
        'justify',
        'float',
        'hide',
        'alert',
        'note',
        'div',
        'spoiler',

        // code filter
        'code',
        'source',
        'var',

        // default filter
        'b',
        'i',
        'u',
        's',
        'sub',
        'sup',
        'abbr',
        'br',
        'hr',
        'time',

        // email filter
        'email',
        'mail',

        // image filter
        'img',
        'image',

        // list filter
        'olist',
        'list',
        'li',
        '*',

        // quote filter
        'quote',

        // table filter
        'table',
        'thead',
        'tbody',
        'tfoot',
        'tr',
        'row',
        'td',
        'col',
        'th',

        // text filter
        'font',
        'size',
        'color',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',

        // url filter
        'url',
        'link',

        // video filter
        'video',
        'youtube',
        'vimeo',
        'veoh',
        'vevo',
        'liveleak',
        'dailymotion',
        'myspace',
        'collegehumor',
        'funnyordie',
        'wegame',

        // preview filter
        'preview',
        'proceed',

        // asset filter
        'asset'
    ]);

    var toolbar = [
        {
            tag: 'h1',
            text: 'H1',
            template: '[h1]{selection}|[/h1]'
        },

        {
            tag: 'h2',
            text: 'H2',
            template: '[h2]{selection}|[/h2]'
        },

        {
            tag: 'h3',
            text: 'H3',
            template: '[h3]{selection}|[/h3]'
        },

        {
            tag: 'h4',
            text: 'H4',
            template: '[h4]{selection}|[/h4]'
        },

        '---', // ---------

        {
            tag: 'b',
            text: '<i class="fa fa-bold" />',
            template: '[b]{selection}|[/b]'
        },

        {
            tag:  'i',
            text: '<i class="fa fa-italic" />',
            template: '[i]{selection}|[/i]'
        },

        {
            tag: 'u',
            text: '<i class="fa fa-underline" />',
            template: '[u]{selection}|[/u]'
        },

        {
            tag: 's',
            text: '<i class="fa fa-strikethrough" />',
            template: '[s]{selection}|[/s]'
        },

        '---', // ---------

        {
            tag: 'sup',
            text: '<i class="fa fa-superscript" />',
            template: '[sup]{selection}|[/sup]'
        },

        {
            tag: 'sub',
            text: '<i class="fa fa-subscript" />',
            template: '[sub]{selection}|[/sub]'
        },

        '---', // ---------

        {
            tag: 'size',
            text: '<i class="fa fa-text-height" />',
            template: '[size="|"]{selection}[/size]'
        },

        {
            tag: 'color',
            text: '<i class="fa fa-tint" />',
            template: '[color="|"]{selection}[/color]'
        },

        '---', // ---------

        {
            tag: 'link',
            text: '<i class="fa fa-link" />',
            template: '[link="|"]{selection}[/link]'
        },

        {
            tag: 'image',
            text: '<i class="fa fa-picture-o" />',
            template: '[image]|[/image]'
        },

        '---', // ---------

        {
            tag: 'video',
            text: '<i class="fa fa-video-camera" />',
            template: '[video="youtube"]|[/video]'
        },

        '---', // ---------

        {
            tag: 'list',
            text: '<i class="fa fa-list-ul" />',
            template: '[list]|[/list]'
        },

        {
            tag: 'olist',
            text: '<i class="fa fa-list-ol" />',
            template: '[olist]|[/olist]'
        },

        {
            tag: 'li',
            text: 'li',
            template: '[li]{selection}|[/li]'
        },

        '---', // ---------

        {
            tag: 'preview',
            text: '<i class="fa fa-eye" />',
            template: '[preview]{selection}|[/preview]'
        },

        {
            tag: 'proceed',
            text: '<i class="fa fa-external-link" />',
            template: '[proceed]{selection}|[/proceed]'
        },

        '---', // ---------

        {
            tag: 'asset',
            hidden: true,
            template: '[asset="{asset}" title="|{title}" align="{align}" /]'
        }
    ];

    $.editor({ tags: tags, toolbar: toolbar });
})();
