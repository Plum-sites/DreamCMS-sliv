<template>
    <div :class="'inner ' + (this.loading ? 'unload' : '')">
        <div class="headline text-center">
            <h2>Набор модераторов</h2>
            <p>Модератор — это неотъемлемая частичка проекта, человек, берущий на себя великую ношу по защите наших серверов от недобросовестного пользователя. Такому человеку администрация вменяет определенный набор полномочий в обмен на безвозмездное служение на благо нашего проекта!</p>
        </div>
        <div class="moder">
            <div class="section primary">
                <h3>Заявка в модераторы</h3>
<!--                <p>Хочешь отличиться среди прочих и считаешь, что именно ты готов на великие свершения? Звание модератора — это отличная возможность проверить себя и свои силы! </p>-->

                <p>
                    На данный момент набор в модераторы временно приостановлен. Набор снова будет открыт ориентировочно в первой половине марта (текущего 2021 года, разумеется :D).
                </p>

                <div class="fieldset current" v-if="current">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <h5>Статус заявки</h5>
                            <small>Отправлено {{ moment.unix(current.time).format('lll') }}</small>
                            <small>Создать новую возможно после:</small>
                            <small>{{ moment.unix(current.time + (7 * 24 * 60 * 60)).format('lll') }}</small>
                        </div>
                        <div class="col" v-if="current.status === 'WAIT'">
                            <h5>Ожидает рассмотрения</h5>
                            <small v-if="current.can_delete"><a href="#" @click="deleteCurrent">Удалить заявку</a></small>
                        </div>
                        <div class="col success" v-if="current.status === 'ACCEPT'">
                            <h5>Поздравляем, Ваша заявка одобрена! </h5>
                            <small>Ожидайте, когда с вами свяжутся!</small>
                        </div>
                        <div class="col failed" v-if="current.status === 'DENY'">
                            <h5>Ваша заявка была отклонена</h5>
                            <small><b>Комментарий:</b> {{ current.answer ? current.answer : 'Без комментария' }}</small>
                        </div>
                        <div class="col failed" v-if="current.status === 'DENY_FULL'">
                            <h5>Ваша заявка была отклонена без возможности повторной подачи</h5>
                            <small><b>Комментарий:</b> {{ current.answer ? current.answer : 'Без комментария' }}</small>
                        </div>
                    </div>
                </div>
                <div class="fieldset current" v-if="current">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <h5>Как вас зовут</h5>
                        </div>
                        <div class="col">
                            <input class="form-control form-control-lg" :value="current.fio" disabled>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-12 col-sm-4">
                            <h5>Сколько вам лет</h5>
                        </div>
                        <div class="col">
                            <input class="form-control form-control-lg" :value="current.old" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <h5>Место жительства</h5>
                        </div>
                        <div class="col">
                            <input class="form-control form-control-lg" :value="current.city" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <h5>Контактные данные</h5>
                        </div>
                        <div class="col">
                            <input class="form-control form-control-lg" :value="current.contacts" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <h5>О себе</h5>
                        </div>
                        <div class="col">
                            <textarea class="form-control form-control-lg" disabled>{{ current.about }}</textarea>
                        </div>
                    </div>
                </div>
                <!--<div class="fieldset" v-else>
                    <div class="row">
                        <h5 class="col-12 col-sm-4">Выберите сервер</h5>
                        <div class="col">
                            <b-select class="form-control form-control-lg" :options="servers" value-field="id" text-field="name" v-model="entry.server"></b-select>
                        </div>
                    </div>
                </div>
                <div class="general">
                    <div class="fieldset" data-key="survivalmg" v-if="entry.server === 40 || entry.server === 50 || entry.server === 34" style="display: block">
                        <div class="row align-items-center">
                            <div class="col-4 view d-none d-sm-block d-lg-none d-xl-block"></div>
                            <div class="col">
                                <h4>Одну минуточку! Кажется, ошибочка тут вышла...</h4>
                                <p>Вы подаёте заявление в модераторы на сервера SurvivalMG, но так вышло, что их приём ведётся не по этому адресу, честно-честно, мы не виноваты!</p>
                                <p>Он ведётся на форуме по ссылкам ниже, Вашу заявку ждут именно там, не перепутайте!</p>
                                <p>
                                    <a href="https://streamcraft.net/forum/discussion/SurvivalMG3/forma-podachi-zayavki-v-personal" class="btn_common mr-2 mb-2 mb-md-0" target="_blank">JediCraft</a>
                                    или <a href="https://streamcraft.net/forum/discussion/CosmoPrison3/forma-podachi-zayavki-v-personal-1" class="btn_common" target="_blank">CosmoPrison</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="fieldset" data-key="minigames" v-else-if="entry.server === 30" style="display: block">
                        <div class="row align-items-center">
                            <div class="col-4 view d-none d-sm-block d-lg-none d-xl-block"></div>
                            <div class="col">
                                <h4>Упс, минуточку внимания!</h4>
                                <p>Вы хотите стать модератором MiniGames? Похвально! Однако набор модераторов на него проводится не здесь, честное администраторское слово!</p>
                                <p>Чтобы подать заявку, перейдите по ссылке ниже, главное тут ничего не перепутать!</p>
                                <p>
                                    <a href="https://streamcraft.net/forum/category/nabor_mg" class="btn_common mr-2" target="_blank">Minigames: подать заявку</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="fieldset" data-key="mods" style="display: block" v-else-if="entry.server">
                        <div class="row align-items-center">
                            <div class="col-12 col-sm-4">
                                <h5>Как вас зовут</h5>
                            </div>
                            <div class="col">
                                <input class="form-control form-control-lg" placeholder="Имя, фамилия и отчество" v-model="entry.fio">
                            </div>
                        </div>
                        <div class="row align-items-center my-2">
                            <div class="col-12 col-sm-4">
                                <h5>Сколько вам лет</h5>
                            </div>
                            <div class="col">
                                <input class="form-control form-control-lg" placeholder="Возраст" v-model="entry.age">
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-12 col-sm-4">
                                <h5>Место жительства</h5>
                            </div>
                            <div class="col">
                                <input class="form-control form-control-lg" placeholder="Город" v-model="entry.city">
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-12 col-sm-4">
                                <h5>Контактные данные</h5>
                            </div>
                            <div class="col">
                                <input class="form-control form-control-lg" placeholder="ВКонтакте и Discord" v-model="entry.contacts">
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-12 col-sm-4">
                                <h5>О себе</h5>
                            </div>
                            <div class="col">
                                <textarea class="form-control form-control-lg" placeholder="Вы многогранный и разносторонний человек? Расскажите о себе и своих увлечениях, это должно быть мини-сочинения не менее, чем на 150 слов!" rows="4" v-model="entry.about"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-end mt-3">
                            <div class="col-12 col-sm-8">
                                <a href="#" class="btn_common large" @click.prevent="sendModerEntry">Отправить заявку</a>
                            </div>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
</template>

<script>
    import api from "../api";
    import {mapGetters} from "vuex";
    import moment from "moment";

    export default {
        name: "ModerEntry",
        computed:{
            ...mapGetters(['user', 'servers'])
        },
        data(){
            return {
                loading: true,

                current: null,

                entry:{
                    server: null,
                    fio: '',
                    age: '',
                    city: '',
                    contacts: '',
                    about: '',
                }
            }
        },
        mounted(){
            this.loadCurrent();
        },
        methods:{
            deleteCurrent(){
              this.loading = true;

              api.post('/moder/delete', this.entry).finally(() => {
                  this.loading = false;
              });
            },
            loadCurrent(){
                this.loading = true;

                api.get('moder/load').then(response => {
                    this.current = response.data.current;
                }).finally(() =>{
                    this.loading = false;
                });
            },
            sendModerEntry(){
                this.loading = true;

                api.post('/moder/send', this.entry)
                .then(response => {
                }).finally(() =>{
                    this.loading = false;
                });
            }
        }
    }
</script>