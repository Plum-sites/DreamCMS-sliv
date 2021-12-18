<template>
  <li
    class="nav-item"
    :class="{'sidebar-group-active active': isActive}"
  >
    <b-link
      class="nav-link"
      :to="{ name: item.route }"
    >
      <feather-icon
        size="14"
        :icon="item.icon"
      />
      <span>{{ item.title }}</span>
    </b-link>
  </li>
</template>

<script>
import { BLink } from 'bootstrap-vue'
import useHorizontalNavMenuHeaderLink from './useHorizontalNavMenuHeaderLink'
import mixinHorizontalNavMenuHeaderLink from './mixinHorizontalNavMenuHeaderLink'

export default {
  components: {
    BLink,
  },
  mixins: [mixinHorizontalNavMenuHeaderLink],
  props: {
    item: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const { isActive, updateIsActive } = useHorizontalNavMenuHeaderLink(props.item)

    return {
      isActive,
      updateIsActive,
    }
  },
}
</script>
