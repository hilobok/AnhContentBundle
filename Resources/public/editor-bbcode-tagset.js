var editor_bbcode_tagset = [
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
        text: '<i class="icon-bold" />',
        template: '[b]{selection}|[/b]'
    },

    {
        tag:  'i',
        text: '<i class="icon-italic" />',
        template: '[i]{selection}|[/i]'
    },

    {
        tag: 'u',
        text: '<i class="icon-underline" />',
        template: '[u]{selection}|[/u]'
    },

    {
        tag: 's',
        text: '<i class="icon-strikethrough" />',
        template: '[s]{selection}|[/s]'
    },

    '---', // ---------

    {
        tag: 'sup',
        text: '<i class="icon-superscript" />',
        template: '[sup]{selection}|[/sup]'
    },

    {
        tag: 'sub',
        text: '<i class="icon-subscript" />',
        template: '[sub]{selection}|[/sub]'
    },

    '---', // ---------

    {
        tag: 'size',
        text: '<i class="icon-text-height" />',
        template: '[size="|"]{selection}[/size]'
    },

    {
        tag: 'color',
        text: '<i class="icon-tint" />',
        template: '[color="|"]{selection}[/color]'
    },

    '---', // ---------

    {
        tag: 'link',
        text: '<i class="icon-link" />',
        template: '[link="|"]{selection}[/link]'
    },

    {
        tag: 'image',
        text: '<i class="icon-picture" />',
        template: '[image]|[/image]'
    },

    '---', // ---------

    {
        tag: 'video',
        text: '<i class="icon-facetime-video" />',
        template: '[video="youtube"]|[/video]'
    },

    '---', // ---------

    {
        tag: 'list',
        text: '<i class="icon-list-ul" />',
        template: '[list]|[/list]'
    },

    {
        tag: 'olist',
        text: '<i class="icon-list-ol" />',
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
        text: '<i class="icon-eye-open" />',
        template: '[preview]{selection}|[/preview]'
    },

    {
        tag: 'proceed',
        text: '<i class="icon-external-link" />',
        template: '[proceed]{selection}|[/proceed]'
    },

    '---', // ---------

    {
        tag: 'asset',
        hidden: true,
        template: '[asset="{asset}" title="|" align="{align}" /]'
    }
];
