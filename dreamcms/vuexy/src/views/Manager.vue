<template>
  <div>
    <b-card>
      <b-card-title>
        Выбор игрока
      </b-card-title>
      <user-selector @input="onSelectUser"></user-selector>
    </b-card>

    <div v-if="loaded">
      <b-row class="mt-2">
        <b-col
            cols="12"
            xl="7"
            lg="6"
            md="5"
        >
          <b-card>
            <b-row>
              <b-col
                  cols="21"
                  xl="6"
                  class="d-flex justify-content-between flex-column"
              >
                <div class="d-flex justify-content-start">
                  <b-avatar
                      :src="getHeadUrl(user.uuid)"
                      :text="user.login"
                      variant="light-primary"
                      size="104px"
                      rounded
                  />
                  <div class="d-flex flex-column ml-1">
                    <div class="mb-1">
                      <h4 class="mb-0">
                        {{ user.login }}
                      </h4>
                      <span class="card-text">
                      <span v-html="user.prefix"></span>
                    </span>
                    </div>
                    <div class="d-flex flex-wrap">
                      <b-button
                          variant="primary"
                          @click="loadUserData(user)"
                      >
                        Обновить
                      </b-button>
                    </div>
                  </div>
                </div>

                <!-- User Stats -->
                <div class="d-flex align-items-center mt-2">
                  <div class="d-flex align-items-center mr-2">
                    <b-avatar
                        variant="light-primary"
                        rounded
                    >
                      <feather-icon
                          icon="DollarSignIcon"
                          size="18"
                      />
                    </b-avatar>
                    <div class="ml-1">
                      <h5 class="mb-0">
                        {{ parseFloat(user.realmoney) + parseFloat(balance.rub) }}
                      </h5>
                      <small :class="(parseInt(balance.rub) !== 0) ? 'text-warning' : ''">Стримы</small>
                    </div>
                  </div>
                </div>
              </b-col>

              <!-- Right Col: Table -->
              <b-col
                  cols="12"
                  xl="6"
              >
                <table class="mt-2 mt-xl-0 w-100">
                  <tr>
                    <th class="pb-50">
                      <feather-icon
                          icon="UserIcon"
                          class="mr-75"
                      />
                      <span class="font-weight-bold">UUID</span>
                    </th>
                    <td class="pb-50">
                      {{ user.uuid }}
                    </td>
                  </tr>
                  <tr>
                    <th class="pb-50">
                      <feather-icon
                          icon="UserPlusIcon"
                          class="mr-75"
                      />
                      <span class="font-weight-bold">Регистрация</span>
                    </th>
                    <td class="pb-50">
                      {{ formatUnix(user.reg_time) }}
                    </td>
                  </tr>
                  <tr>
                    <th class="pb-50">
                      <feather-icon
                          icon="CheckIcon"
                          class="mr-75"
                      />
                      <span class="font-weight-bold">Статус</span>
                    </th>
                    <td class="pb-50 text-danger" v-if="activeBan">
                      Заблокирован
                    </td>
                    <td class="pb-50 text-success" v-else>
                      Активен
                    </td>
                  </tr>
                  <tr>
                    <th class="pb-50">
                      <feather-icon
                          icon="CalendarIcon"
                          class="mr-75"
                      />
                      <span class="font-weight-bold">Был в игре</span>
                    </th>
                    <td class="pb-50">
                      {{ formatUnix(user.last_play) }}
                    </td>
                  </tr>
                  <tr>
                    <th class="pb-50">
                      <feather-icon
                          icon="MailIcon"
                          class="mr-75"
                      />
                      <span class="font-weight-bold">Email</span>
                    </th>
                    <td :class="'pb-50 ' + (user.email_confirmed_at ? 'text-success' : 'text-warning')">
                      {{ user.email }}
                    </td>
                  </tr>
                  <tr>
                    <th class="pb-50">
                      <feather-icon
                          icon="StarIcon"
                          class="mr-75"
                      />
                      <span class="font-weight-bold">Репутация</span>
                    </th>
                    <td :class="'pb-50 ' + (parseInt(balance.rep) !== 0 ? 'text-warning' : '')">
                      {{ parseInt(user.reputation) + parseInt(balance.rep) }}
                    </td>
                  </tr>
                </table>
              </b-col>
            </b-row>
          </b-card>
        </b-col>

        <b-col
            cols="12"
            xl="3"
            lg="3"
            md="4"
        >
          <b-card bg-variant="primary" text-variant="white">
            <b-row>
              <b-col sm="12">
                <b-form-group>
                  <b-button variant="danger" block @click="clear2FA">Удалить 2FA</b-button>
                </b-form-group>
              </b-col>
              <b-col sm="12">
                <b-form-group>
                  <b-button variant="danger" block @click="clearServerPerms">Очистить PEX на серверах</b-button>
                </b-form-group>
              </b-col>
              <b-col sm="12">
                <b-form-group>
                  <b-button variant="danger" block @click="clearSitePerms">Очистить права на сайте</b-button>
                </b-form-group>
              </b-col>
            </b-row>
          </b-card>
        </b-col>

        <b-col cols="12"
               xl="2"
               lg="3"
               md="3">
          <b-card bg-variant="warning" text-variant="white">
            <b-row>
              <b-col sm="12">
                <b-form-group label="Изменение стримов">
                  <b-form-input type="number" v-model="balance.rub" required autocomplete="off"/>
                </b-form-group>
              </b-col>
              <b-col sm="12">
                <b-form-group label="Изменение репутации">
                  <b-form-input type="number" v-model="balance.rep" required autocomplete="off"/>
                </b-form-group>
              </b-col>
              <b-col sm="12">
                <b-button variant="danger" @click="updateBalance">Применить</b-button>
              </b-col>
            </b-row>
          </b-card>
        </b-col>
      </b-row>

      <b-row>
        <b-col
            sm="12"
            md="6"
        >
          <b-card>
            <b-card-title>
              Игровые блокировки

              <b-button
                  variant="outline-danger"
                  size="sm"
                  class="float-right"
                  @click="modals.ban_game = true"
              >
                <feather-icon size="16" icon="PlusIcon"></feather-icon>
              </b-button>
            </b-card-title>
            <b-table
                class="position-relative"
                :items="user.bans.game"
                :fields="gameBansHeaders"
                responsive
                primary-key="id"
                show-empty
                empty-text="Не найдено"
            >
              <template #cell(admin)="row">
                {{ row.item.admin.login }}
              </template>

              <template #cell(Time)="row">
                {{ formatUnix(row.item.Time) }}
              </template>

              <template #cell(Temptime)="row">
                <span v-if="row.item.Temptime" :class="row.item.Temptime < unix ? 'text-success' : 'text-warning'">{{
                    formatUnix(row.item.Temptime)
                  }}</span>
                <span class="text-danger" v-else>Навсегда</span>
              </template>

              <template #cell(actions)="row">
                <b-button size="sm" variant="danger" @click="unbanGame(row.item.id)"
                          v-if="hasPermission('manager.ban_game.delete')">
                  <feather-icon
                      icon="TrashIcon"
                      size="21"
                  />
                </b-button>
              </template>
            </b-table>
          </b-card>
        </b-col>
        <b-col
            sm="12"
            md="6"
        >
          <b-card>
            <b-card-title>
              Блокировки форума

              <b-button
                  variant="outline-danger"
                  size="sm"
                  class="float-right"
                  @click="modals.ban_forum = true"
              >
                <feather-icon size="16" icon="PlusIcon"></feather-icon>
              </b-button>
            </b-card-title>
            <b-table
                class="position-relative"
                :items="user.bans.forum"
                :fields="forumBansHeaders"
                responsive
                primary-key="id"
                show-empty
                empty-text="Не найдено"
            >
              <template #cell(created_at)="row">
                {{ formatDate(row.item.created_at) }}
              </template>

              <template #cell(expired_at)="row">
                {{ formatDate(row.item.expired_at) }}
              </template>

              <template #cell(actions)="row">
                <b-button size="sm" variant="danger" @click="unbanForum(row.item.id)"
                          v-if="hasPermission('manager.ban_site.delete')">
                  <feather-icon
                      icon="TrashIcon"
                      size="21"
                  />
                </b-button>
              </template>
            </b-table>
          </b-card>
        </b-col>
      </b-row>

      <b-row>
        <b-col sm="12" md="6">
          <b-card>
            <b-card-title>
              Активные донат группы

              <b-button
                  variant="outline-success"
                  size="sm"
                  class="float-right"
                  @click="modals.donate_manage = true"
              >
                <feather-icon size="16" icon="PlusIcon"></feather-icon>
              </b-button>
            </b-card-title>
            <b-table
                class="position-relative"
                :items="user.status"
                :fields="statusHeaders"
                responsive
                primary-key="id"
                show-empty
                empty-text="Не найдено"
            >
              <template #cell(server)="row">
                {{ row.item.server.name }}
              </template>

              <template #cell(group)="row">
                {{ row.item.group.name }}
              </template>

              <template #cell(time)="row">
                {{ formatUnix(row.item.time) }}
              </template>

              <template #cell(expire)="row">
                {{ formatUnix(row.item.expire) }}
              </template>

              <template #cell(actions)="row">
                <b-button size="sm" variant="danger" @click="removeDonate(row.item.id)"
                          v-if="hasPermission('manager.donate.revoke')">
                  <feather-icon
                      icon="TrashIcon"
                      size="21"
                  />
                </b-button>
              </template>
            </b-table>
          </b-card>
        </b-col>

        <b-col sm="12" md="6">
          <b-card>
            <b-card-title>
              Активные группы на серверах

              <b-button
                  variant="outline-success"
                  size="sm"
                  class="float-right"
                  @click="modals.group_manage = true"
              >
                <feather-icon size="16" icon="PlusIcon"></feather-icon>
              </b-button>
            </b-card-title>
            <b-table
                :busy.sync="user_pex.loading"
                class="position-relative"
                :items="user_pex.list"
                :fields="pexHeaders"
                responsive
                primary-key="id"
                show-empty
                empty-text="Не найдено"
            >
              <template #cell(server)="row">
                {{ row.item.server.name }}
              </template>

              <template #table-busy>
                <div class="text-center text-primary my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Загрузка...</strong>
                </div>
              </template>
            </b-table>
          </b-card>
        </b-col>
      </b-row>

      <b-row>
        <b-col sm="12">
          <b-card>
            <b-card-title>
              Предметы в корзине

              <b-button
                  variant="outline-success"
                  size="sm"
                  class="float-right"
                  @click="modals.cart_manage = true"
              >
                <feather-icon size="16" icon="PlusIcon"></feather-icon>
              </b-button>
            </b-card-title>
            <b-table
                :items="user.cart"
                :fields="cartHeaders"
                primary-key="id"
                show-empty
                empty-text="Не найдено"
            >
              <template #cell(actions)="row">
                <b-button size="sm" variant="danger" @click="removeCartItem(row.item.id)" v-if="hasPermission('manager.cart.revoke')">
                  <feather-icon
                      icon="TrashIcon"
                      size="21"
                  />
                </b-button>
              </template>
            </b-table>
          </b-card>
        </b-col>
      </b-row>
    </div>

    <b-modal v-model="modals.ban_game"
             id="modal-gameban"
             ok-only
             ok-variant="danger"
             ok-title="Заблокировать"
             cancel-title="Отмена"
             modal-class="modal-danger"
             centered
             title="Выдача блокировки в игре"
             @ok="banGame"
    >
      <b-row>
        <b-col sm="12" md="6">
          <b-form-group label="Причина">
            <b-form-input v-model="ban.game.reason"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col sm="12" md="6">
          <b-form-group label="Сервер">
            <v-select v-model="ban.game.server" :options="servers" label="name" :reduce="item => item.id"></v-select>
          </b-form-group>
        </b-col>
        <b-col sm="12" md="6">
          <b-form-group label="Срок действия">
            <flat-pickr
                v-model="ban.game.time"
                :config="rangePickerConfig"
                class="form-control"
            />
          </b-form-group>
        </b-col>
      </b-row>
    </b-modal>

    <b-modal v-model="modals.ban_forum"
             id="modal-forumban"
             ok-only
             ok-variant="danger"
             ok-title="Заблокировать"
             cancel-title="Отмена"
             modal-class="modal-danger"
             centered
             title="Выдача блокировки на форуме"
             @ok="banForum"
    >
      <b-row>
        <b-col sm="12" md="6">
          <b-form-group label="Причина">
            <b-form-input v-model="ban.forum.reason"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col sm="12" md="6">
          <b-form-group label="Срок действия">
            <flat-pickr
                v-model="ban.forum.time"
                :config="rangePickerConfig"
                class="form-control"
            />
          </b-form-group>
        </b-col>
      </b-row>
    </b-modal>

    <b-modal v-model="modals.donate_manage"
             id="modal-donate"
             modal-class="modal-success"
             centered
             title="Управление донат группами"
             hide-footer
    >
      <b-card-title>
        Выдача донат группы
      </b-card-title>
      <b-form-group label="Выберите группу">
        <v-select v-model="donate.give.group" :options="dgroups" label="name"
                  :reduce="item => item.id"></v-select>
      </b-form-group>
      <b-form-group label="Выберите сервер">
        <v-select v-model="donate.give.server" :options="servers" label="name"
                  :reduce="item => item.id"></v-select>
      </b-form-group>
      <b-form-group label="Срок действия">
        <flat-pickr
            v-model="donate.give.time"
            :config="rangePickerConfig"
            class="form-control"
        />
      </b-form-group>
      <b-button variant="warning" @click="giveDonate" v-if="hasPermission('manager.donate.give')">
        Выдать группу
      </b-button>
    </b-modal>

    <b-modal v-model="modals.group_manage"
             id="modal-donate"
             modal-class="modal-success"
             centered
             title="Управление правами на серверах"
             hide-footer
    >
      <b-form-group label="Группа">
        <b-form-input type="number" v-model="group.give.group"></b-form-input>
      </b-form-group>
      <b-form-group label="Выберите сервер">
        <v-select v-model="group.give.server" :options="servers" label="name"
                  :reduce="item => item.id"></v-select>
      </b-form-group>
      <b-form-group label="Время действия">
        <flat-pickr
            v-model="group.give.time"
            :config="rangePickerConfig"
            class="form-control"
        />
      </b-form-group>
      <b-button variant="success" @click="giveGroup" v-if="hasPermission('manager.permissions.give')">
        Выдать группу
      </b-button>
      <b-button variant="danger" @click="revokeGroup" v-if="hasPermission('manager.permissions.revoke')">
        Снять группу
      </b-button>

      <hr>

      <b-button variant="danger" @click="clearServerPerms" v-if="hasPermission('manager.permissions.revoke')">
        Снять права со всех серверов
      </b-button>
    </b-modal>

    <b-modal v-model="modals.cart_manage"
             id="modal-cart"
             size="xl"
             modal-class="modal-success"
             centered
             title="Выдача предметов и китов"
             hide-footer
    >
      <b-row>
        <b-col sm="12" md="6">
          <b-card-title>
            Выдача предмета
          </b-card-title>

          <b-form-group label="Выберите предмет">
            <item-selector v-model="cart.give.item"></item-selector>
          </b-form-group>
          <b-form-group label="Выберите сервер">
            <v-select v-model="cart.give.server" :options="servers" label="name" :reduce="item => item.id"></v-select>
          </b-form-group>
          <b-form-group label="Количество">
            <b-form-input type="number" v-model="cart.give.count"></b-form-input>
          </b-form-group>
          <b-button variant="success" @click="giveCartItem" v-if="hasPermission('manager.cart.give')">
            Выдать предмет
          </b-button>
        </b-col>
        <b-col sm="12" md="6">
          <b-card-title>
            Выдача кита
          </b-card-title>

          <b-form-group label="Введите название кита">
            <b-form-input v-model="cart.kit_give.name"></b-form-input>
          </b-form-group>
          <b-form-group label="Выберите сервер">
            <v-select v-model="cart.kit_give.server" :options="servers" label="name"
                      :reduce="item => item.id"></v-select>
          </b-form-group>
          <b-button variant="success" @click="giveCartKit" v-if="hasPermission('manager.cart_kit.give')">
            Выдать кит
          </b-button>
        </b-col>
      </b-row>
    </b-modal>
  </div>

</template>

<script>
import {mapGetters} from "vuex";
import vSelect from 'vue-select';
import flatPickr from 'vue-flatpickr-component'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

import UserSelector from "../components/UserSelector";
import ItemSelector from '../components/ItemSelector';

import {
  BCard, BButton, BAvatar, BRow, BCol, BFormInput, BFormCheckbox, BSpinner, BForm, BTable, BFormGroup, BCardTitle
} from 'bootstrap-vue'

import api from '../api';
import {Russian} from 'flatpickr/dist/l10n/ru.js';
import moment from "moment";

export default {
  name: "ManageUser",
  components: {
    UserSelector,
    ItemSelector,
    BCard,
    BButton,
    BAvatar,
    BRow,
    BCol,
    BFormInput,
    BFormCheckbox,
    BSpinner,
    BForm,
    BTable,
    BFormGroup,
    vSelect,
    flatPickr,
    AppCollapse,
    AppCollapseItem,
    BCardTitle
  },
  data: function () {
    return {
      user: null,
      loaded: false,

      modals: {
        ban_game: false,
        ban_forum: false,
        donate_manage: false,
        group_manage: false,
        cart_manage: false,
      },

      rangePickerConfig: {
        mode: 'range',
        locale: Russian,
        defaultDate: [moment().toDate(), moment().add(31, "day").toDate()],
      },

      forumBansHeaders: [
        {
          key: 'comment',
          label: 'Причина',
          sortable: false
        },
        {
          key: 'created_at',
          label: 'Создан',
          sortable: false
        },
        {
          key: 'expired_at',
          label: 'Окончание',
          sortable: false
        },
        {
          key: 'actions',
          label: 'Действия',
          sortable: false
        },
      ],

      gameBansHeaders: [
        {
          key: 'Reason',
          label: 'Причина',
          sortable: false
        },
        {
          key: 'Time',
          label: 'Начало',
          sortable: false
        },
        {
          key: 'Temptime',
          label: 'Окончание',
          sortable: false
        },
        {
          key: 'admin',
          label: 'Администратор',
          sortable: false
        },
        {
          key: 'actions',
          label: 'Действия',
          sortable: false
        },
      ],

      statusHeaders: [
        {
          key: 'server',
          label: 'Сервер',
          sortable: false
        },
        {
          key: 'group',
          label: 'Группа',
          sortable: false
        },
        {
          key: 'time',
          label: 'Начало',
          sortable: false
        },
        {
          key: 'expire',
          label: 'Окончание',
          sortable: false
        },
        {
          key: 'actions',
          label: "Действия",
          sortable: false
        }
      ],

      pexHeaders: [
        {
          key: 'server',
          label: 'Сервер',
          sortable: false
        },
        {
          key: 'name',
          label: 'Группа',
          sortable: false
        }
      ],

      cartHeaders: [
        {
          key: 'id',
          label: 'ID',
          sortable: false
        },
        {
          key: 'shop',
          label: 'Магазин',
          sortable: false
        },
        {
          key: 'type',
          label: 'Meta',
          sortable: false
        },
        {
          key: 'damage',
          label: 'Damage',
          sortable: false
        },
        {
          key: 'count',
          label: 'Кол-во',
          sortable: false
        },
        {
          key: 'enchants',
          label: 'Зачарования',
          sortable: false
        },
        {
          key: 'nbt',
          label: 'NBT',
          sortable: false
        },
        {
          key: 'actions',
          label: 'Действия',
          sortable: false
        }
      ],

      user_pex: {
        list: [],
        loading: true
      },
      balance: {
        rub: 0,
        mon: 0,
        rep: 0
      },
      donate: {
        give: {
          server: null,
          group: null,
          time: null
        }
      },
      cart: {
        give: {
          server: null,
          count: 0,
          item: null
        },
        kit_give: {
          name: null,
          server: null
        }
      },
      group: {
        give: {
          server: null,
          group: null,
          time: null
        },
        revoke: {
          server: null,
          group: null
        }
      },
      ban: {
        game: {
          server: null,
          reason: null,
          time: null
        },
        forum: {
          reason: null,
          time: null
        }
      }
    }
  },
  computed: {
    ...mapGetters(["servers", "dgroups"]),
    activeBan() {
      return this.user.bans.game.find(ban => {
        return ban.Temptime ? ban.Temptime > moment().unix() : true;
      });
    },
    unix() {
      return moment().unix();
    }
  },
  methods: {
    formatUnix(timestamp) {
      return moment.unix(timestamp).format('lll');
    },
    formatDate(date) {
      return moment(date).format('lll');
    },
    findById(array, id) {
      return array.find(obj => obj.id === parseInt(id));
    },
    onSelectUser(user) {
      this.loaded = false;

      this.user = user;

      if (user != null) {
        this.loadUserData(user);
      }
    },
    loadUserData(user) {
      api.post('manager/user', {id: user.id})
          .then(response => {
            this.user = response.data.user;
            this.user.cart = response.data.cart;
            this.user.bans = response.data.bans;
            this.user.status = response.data.status;
            this.user.groups = [];

            response.data.status.map((value, key) => {
              value.group = this.findById(this.dgroups, value.group_id);
              value.server = this.findById(this.servers, value.server_id);
              console.log(value);
              this.user.groups.push(value);
            });

            console.log(this.user);

            this.user_pex.loading = true;
            this.loaded = true;

            api.post('manager/user/pex', {id: user.id})
                .then(response => {
                  this.user_pex.list = response.data.groups;

                  this.user_pex.list.map((value, key) => {
                    value.server_id = value.server;
                    value.server = this.findById(this.servers, value.server);
                    return value;
                  });

                  this.user_pex.loading = false;
                })
                .catch(err => {
                  console.log(err);
                  App.showError(err.message);
                });
          })
          .catch(err => {
            console.log(err);
            App.showError(err.message);
          });
    },
    sendRequest(url, data, callback) {
      api.post(url, data)
          .then(response => {
            if (callback !== null) {
              callback(response);
            }
          });
    },
    updateBalance() {
      this.sendRequest('manager/user/balance', {
            user: this.user.id,
            realmoney: this.balance.rub,
            money: this.balance.mon,
            reputation: this.balance.rep
          },
          () => {
            this.balance.rub = 0;
            this.balance.mon = 0;
            this.balance.rep = 0;
            this.loadUserData(this.user);
          }
      );
    },
    momentTotimespamps(m) {
      return m;
    },
    giveDonate() {
      this.sendRequest('manager/donate/give', {
        user: this.user.id,
        server: this.donate.give.server,
        group: this.donate.give.group,
        time: this.momentTotimespamps(this.donate.give.time)
      });
    },
    removeDonate(id) {
      this.sendRequest('manager/donate/remove', {id: id});
    },
    giveGroup() {
      this.sendRequest('manager/pex/group/add', {
        user: this.user.id,
        server: this.group.give.server,
        group: this.group.give.group,
        time: this.momentTotimespamps(this.group.give.time)
      });
    },
    revokeGroup() {
      this.sendRequest('manager/pex/group/remove', {
        user: this.user.id,
        server: this.group.give.server,
        group: this.group.give.group
      });
    },
    giveCartItem() {
      this.sendRequest('manager/cart/give', {
        user: this.user.id,
        server: this.cart.give.server,
        count: this.cart.give.count,
        item: this.cart.give.item
      });
    },
    giveCartKit() {
      this.sendRequest('manager/cart/kit', {
        user: this.user.id,
        server: this.cart.kit_give.server,
        name: this.cart.kit_give.name
      });
    },
    removeCartItem(id) {
      this.sendRequest('manager/cart/remove', {id: id});
    },
    banGame() {
      this.sendRequest('manager/ban/game', {
        user: this.user.id,
        reason: this.ban.game.reason,
        time: this.momentTotimespamps(this.ban.game.time),
        server: this.ban.game.server
      });
    },
    banForum() {
      this.sendRequest('manager/ban/forum', {
        user: this.user.id,
        reason: this.ban.forum.reason,
        time: this.momentTotimespamps(this.ban.forum.time)
      });
    },
    unbanGame(id) {
      this.sendRequest('manager/unban/game', {id: id});
    },
    unbanForum(id) {
      this.sendRequest('manager/unban/forum', {user: this.user.id});
    },
    clearServerPerms() {
      this.sendRequest('manager/pex/clear', {user: this.user.id});
    },
    clearSitePerms() {
      this.sendRequest('manager/perms/clear', {user: this.user.id});
    },
    loginAsUser() {
      this.sendRequest('manager/login', {user: this.user.id});
    },
    clear2FA() {
      this.sendRequest('manager/user/2fa', {user: this.user.id});
    }
  },

  watch: {
    'donate.give.time': function (val) {
      console.log(val);
    }
  }
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/vue-select.scss';
</style>