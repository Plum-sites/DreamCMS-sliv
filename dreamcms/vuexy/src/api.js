import Vue from 'vue'
import axios from 'axios';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export const LOAD_USER = 'loadUser';
export const READ_NOTIFY = 'markNotificationsAsRead';

const instance = axios.create({
    baseURL: '/api/admin/'
});

instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    instance.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    console.log('CSRF token founded!')
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

token = localStorage.getItem('api-token');
if (token) {
    console.log('API auth token founded!')
    instance.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}else {
    console.error('API access token not found!');
}

instance.interceptors.response.use(function (response) {
    if (response.data.hasOwnProperty('success')){
        if (response.data.success){
            if (response.data.message){
                Vue.$toast({
                    component: ToastificationContent,
                    props: {
                        title: 'Успешно!',
                        icon: 'CheckIcon',
                        text: response.data.message,
                        variant: 'success',
                    },
                });
            }
        }else {
            if (response.data.message){
                Vue.$toast({
                    component: ToastificationContent,
                    props: {
                        title: 'Ошибка!',
                        icon: 'XIcon',
                        text: response.data.message,
                        variant: 'warning',
                    },
                });
            }else {
                if (response.data.errors){
                    Object.values(response.data.errors).forEach(arr => {
                        arr.forEach(err => {
                            Vue.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Ошибка!',
                                    icon: 'XIcon',
                                    text: err,
                                    variant: 'error',
                                },
                            });
                        });
                    });
                }else{
                    Vue.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Ошибка!',
                            icon: 'XIcon',
                            text: 'Произошла неизвестная ошибка, вероятно, не верные данные в запросе',
                            variant: 'error',
                        },
                    });
                }
            }
        }
    }
    return response;
}, function (error) {
    Vue.$toast({
        component: ToastificationContent,
        props: {
            title: 'Ошибка!',
            icon: 'XIcon',
            text: error.message,
            variant: 'error',
        },
    });

    return Promise.reject(error);
});


export default instance;
