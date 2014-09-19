CodeMirror.defineMode('bbcode', function(config, options) {
    var tags = [
        'b', 'i', 's'
    ];

    if (options.hasOwnProperty('tags')) {
        var temp = $.makeArray(options.tags);

        tags = $.grep(temp, function(value, position) {
            return temp.indexOf(value) == position;
        });
    }

    tags = $.map(tags.sort().reverse(), function (val) {
        return (val + '').replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
    });

    var tagsRegExp = new RegExp('(' + tags.join('|') + ')');

    return {
        startState: function() {
            return {
                inTag: false
            };
        },

        token: function(stream, state) {
            var style = null;

            if(!state.inTag && stream.peek() == '[') {
                stream.next();
                state.inTag = true;
            }

            if(state.inTag) {
                if(stream.peek() == '/') {
                    stream.next();
                }

                var match = stream.match(tagsRegExp);

                if(match) {
                    style = 'tag tag-' + (match[1] == '*' ? 'li' : match[1]);
                    stream.backUp(match[1].length);
                }

                if(stream.skipTo(']')) {
                    stream.next();
                    state.inTag = false;
                } else {
                    stream.skipToEnd();
                }
            } else {
                var _linter = stream.skipTo('[') || stream.skipToEnd();
            }

            return style;
        }
    };
});
