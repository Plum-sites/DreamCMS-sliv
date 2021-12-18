<template>
  <div>
    <vue-autosuggest
        size="lg"
        ref="autocomplete"
        v-model="query"
        :suggestions="suggestions"
        :input-props="inputProps"
        :section-configs="sectionConfigs"
        :render-suggestion="renderSuggestion"
        :get-suggestion-value="getSuggestionValue"
        @input="fetchResults"
    />
  </div>
</template>

<script>
    import api from '../api';
    import { VueAutosuggest } from 'vue-autosuggest'
    import { BCard, BCardText, BAvatar } from 'bootstrap-vue'

    export default {
        name: "UserSelector",
        components:{
          VueAutosuggest, BCard, BCardText, BAvatar
        },
        data: function () {
            return {
                query: '',
                isLoading: false,
                suggestions: [],
                selected: null,
                search: null,

                inputProps: {
                    id: 'autosuggest__input_ajax',
                    placeholder: 'Введите ник игрока',
                    class: 'form-control',
                    name: 'ajax',
                },

                sectionConfigs: {
                  users: {
                    limit: 10,
                    label: 'Игроки',
                    onSelected: selected => {
                      this.selected = selected.item
                    },
                  }
                },

                timeout: null,
                debounceMilliseconds: 250,
            }
        },
      methods:{
        fetchResults() {
          const { query } = this

          clearTimeout(this.timeout)
          this.timeout = setTimeout(() => {
            this.suggestions = [];
            api.get('manager/user/find?q=' + query)
            .then(res => {
                this.suggestions.push({ name: 'users', data: this.filterResults(res.data.data, query, 'login') })
            })
            .catch(err => {
                console.log(err)
            });
          }, this.debounceMilliseconds)
        },
        filterResults(data, text, field) {
          return data.filter(item => {
            if (item[field].toLowerCase().indexOf(text.toLowerCase()) > -1) {
              return item[field]
            }
            return false
          }).sort()
        },
        renderSuggestion(suggestion) {
          if (suggestion.name === 'users') {
            const user = suggestion.item
            const url = this.getHeadUrl(user.uuid)
            return (
                <div class="d-flex">
                  <div>
                    <b-avatar src={url} class="mr-50"></b-avatar>
                  </div>
                  <div>
                    <span>{user.login}</span>
                  </div>
                </div>
            )
          }
          return suggestion.item.login
        },
        getSuggestionValue(suggestion) {
          const { name, item } = suggestion
          return item.login
        },
      },
      watch:{
            'selected': function (val) {
                console.log('Selected user:' + JSON.stringify(val));

                this.$emit('input', val);
                this.$emit('selected', val);
            }
        }
    }
</script>

<style lang="scss">
@import '../@core/scss/vue/libs/vue-autosuggest.scss';
pre{
  min-height: 295px;
  padding: 1.5rem;
  margin-bottom: 0;
  border-radius: .5rem;
}
</style>