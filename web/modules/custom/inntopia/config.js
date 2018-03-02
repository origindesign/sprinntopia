module.exports = {
    browserSync: {
        hostname: "sprinntopia.docker.localhost",
        port: 8000,
        openAutomatically: false,
        reloadDelay: 50,
        injectChanges: true,
    },

    drush: {
        enabled: false,
        alias: {
            css_js: 'drush @SITE-ALIAS cc css-js',
            cr: 'drush @SITE-ALIAS cr'
        }
    },

    twig: {
        useCache: true
    }
};
