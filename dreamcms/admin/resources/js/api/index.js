import axios from 'axios';

export const LOAD_USER = 'loadUser';
export const READ_NOTIFY = 'markNotificationsAsRead';

const instance = axios.create({
    baseURL: '/api/admin/'
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
