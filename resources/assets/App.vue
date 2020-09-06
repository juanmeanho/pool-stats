<template>
    <div id="app">
        <router-view :app="this"></router-view>
    </div>
</template>

<script>

import Vue from "vue";
import BootstrapVue from 'bootstrap-vue';
import ToggleButton from 'vue-js-toggle-button'
import store from './store/store'
import vueHeadful from 'vue-headful';
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import ScaleLoader from 'vue-spinner/src/PulseLoader.vue'
import ClipLoader from 'vue-spinner/src/PulseLoader.vue'
import vSelect from 'vue-select'



Vue.use(ToggleButton)
Vue.use(BootstrapVue);

Vue.component('v-select', vSelect)
Vue.component('vue-headful', vueHeadful);
Vue.component('pulse-loader', require('vue-spinner/src/PulseLoader.vue'));
Vue.component('rotate-loader', require('vue-spinner/src/RotateLoader.vue'));
Vue.component('scale-loader', require('vue-spinner/src/ScaleLoader.vue'));
Vue.component('clip-loader', require('vue-spinner/src/ClipLoader.vue'));

import 'vue-select/dist/vue-select.css';


export default {
    name: 'app',
    data(){
    	return{
            user: '', ajuste: 0, timeRefresh: '',
    		req: axios.create({
    			baseUrl: 'http://localhost:8000'
    		})
    	}
    },
    mounted(){
    	this.init();
    },
    methods: {
    	init()
    	{        
            this.getValueBtc();
            this.refreshData();
            axios
                .get('auth/init')
                .then(response => response.data)
                .then(data => {
                    if(data.user){
                        this.$store.commit("setUser", data.user.name)
                        this.$store.commit("setAjuste", data.user.ajuste)
                    }else{
                        this.$store.commit("setUser", null)
                    }
                })
        },
        refreshData() {
            this.timeRefresh = setInterval(() => {    
                this.getValueBtc();
            }, 25000);    
        },
        getValueBtc() {
            axios
                .get('https://blockchain.info/ticker')
                .then( response => response.data) 
                .then(data => {
                    this.$store.commit("setValueBtc", data.USD.last)
                }).catch(error => {
                    console.log('Error de Get Btc Price')
                }); 
        }
    }
}

</script>
<style lang="scss" src="assets/sass/bootstrap/bootstrap.scss"></style>
<style src="font-awesome/css/font-awesome.css"></style>
<style src="bootstrap-vue/dist/bootstrap-vue.css"></style>
