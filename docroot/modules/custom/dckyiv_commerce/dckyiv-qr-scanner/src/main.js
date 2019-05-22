import Vue from 'vue';
import App from './App.vue';
import VueQrcodeReader from "vue-qrcode-reader";

Vue.use(VueQrcodeReader);
console.log('tt');
//Vue.config.productionTip = false

new Vue({
  render: h => h(App),
}).$mount('#app')
