const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
    ...defaultConfig,
    entry: {
        ...defaultConfig.entry,
        'comment-mention': [ './src/js/index.js', './src/css/tribute.css' ],
    }
};
