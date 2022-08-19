import NovaServerMetrics from './components/Card'
Nova.booting((Vue, router) => {
    Vue.component('nova-server-metrics', NovaServerMetrics);
})
