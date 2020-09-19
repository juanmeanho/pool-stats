import 'es6-promise/auto'
import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'

Vue.use(Vuex)

function addDays(noOfDays) {
    return (noOfDays * 24 * 60 * 60 * 1000)
}

//=======vuex store start===========
const store = new Vuex.Store({
    state: {
        valueBtc: 0,
        timeInForceGlobal: 'GTC',
        arrayCoinsByVariation: [],
        stopLossGlobal: 5,
        dollarGlobalProfit: 0,
        percentageGlobalProfit: 0,
        saldoUsd: 0,
        saldoBtc: 0,
        profit: 0,
        variationBtc: 0,
        priceDollar: 0,
        incrementToBuy: 0.00000001,
        decrementToSell: 0.00000001,
        calling: false,
        left_open: true,
        preloader: true,
        site_name: "LivePool",
        page_title: null,
        ajuste: 0,
        user: {
            name: null,
            picture: "/images/user.jpg",
            job: "Project Manager"
        },
        cal_events: [{
            id: 0,
            title: 'Office',
            start: Date.now(),
            end: Date.now() + addDays(1)
        }, {
            id: 1,
            title: 'Holidays',
            start: Date.now() + addDays(3),
            end: Date.now() + addDays(4)
        }],
        // Add your application keys
        //gmap_key: 'AIzaSyBTnQCx3FXEnfWPPWTKAwIxt6wSjAn_8ug',
        //openWeather_key: 'c00194f61244d2b33b863bff6d94e663',
        google_analytics_key: null

    },
    mutations
})
//=======vuex store end===========
export default store
