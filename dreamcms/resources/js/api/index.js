import axios from 'axios';
import Vue from 'vue'

export const AUTH_SUCCESS = 'authSuccess';
export const AUTH_LOGOUT = 'authLogout';

export const LOAD_USER = 'loadUser';

// FORUM
export const FORUM_LOAD_LATEST = 'loadLatestPosts';
export const FORUM_LOAD_LEADERS = 'loadLeaders';
export const FORUM_LOAD_POPULARS = 'loadPopulars';

export const FORUM_LOAD = 'loadForumIndex';

export const READ_NOTIFICATIONS = 'readNotifications';

const instance = axios.create({
    baseURL: '/api/'
});

instance.interceptors.response.use(function (response) {
    if (response.data.hasOwnProperty('success')){
        if (response.data.success){
            if (response.data.message){
                Vue.notify({
                    title: 'Успешно!',
                    text: response.data.message,
                    type: 'success'
                });
            }
        }else {
            if (response.data.message){
                Vue.notify({
                    title: 'Ошибка',
                    text: response.data.message,
                    type: 'warn'
                });
            }else {
                if (response.data.errors){
                    Object.values(response.data.errors).forEach(arr => {
                        arr.forEach(err => {
                            Vue.notify({
                                title: 'Ошибка',
                                text: err,
                                type: 'error'
                            });
                        });
                    });
                }else{
                    Vue.notify({
                        title: 'Ошибка',
                        text: "Произошла неизвестная ошибка, вероятно, не верные данные в запросе",
                        type: 'error'
                    });
                }
            }
        }
    }
    return response;
}, function (error) {
    Vue.notify({
        title: 'Ошибка',
        text: error.message,
        type: 'error'
    });
    return Promise.reject(error);
});


export default instance;
