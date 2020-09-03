import router from '../routes'

const appName = 'vue-audits'

if (document.getElementById(appName) !== null) {
    new Vue({
        el: '#' + appName,

        store: window.Store,

        router,

        data: {},

        methods: {
            
        },

        computed: {
            
        },
    })
}
