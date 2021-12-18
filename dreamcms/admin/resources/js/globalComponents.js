/**
 * Vuely Global Components
 */
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import AppSectionLoader from "./components/AppSectionLoader/AppSectionLoader";
import { RotateSquare2 } from "vue-loading-spinner";

// delete Confirmation Dialog
import DeleteConfirmationDialog from "./components/DeleteConfirmationDialog/DeleteConfirmationDialog";

// page title bar
import PageTitleBar from "./components/PageTitleBar/PageTitleBar";

// App Card component
import AppCard from './components/AppCard/AppCard';

// stats card
import StatsCard from './components/StatsCard/StatsCard';
import StatsCardV2 from './components/StatsCardV2/StatsCardV2';
import StatsCardV4 from './components/StatsCardV4/StatsCardV4';

// section tooltip
import SectionTooltip from "./components/SectionTooltip/SectionTooltip"

const GlobalComponents = {
   install(Vue) {
      Vue.component('appCard', AppCard);
      Vue.component('sectionTooltip', SectionTooltip);
      Vue.component('statsCard', StatsCard);
      Vue.component('statsCardV2', StatsCardV2);
      Vue.component('statsCardV4', StatsCardV4);
      Vue.component('deleteConfirmationDialog', DeleteConfirmationDialog);
      Vue.component('vuePerfectScrollbar', VuePerfectScrollbar);
      Vue.component('appSectionLoader', AppSectionLoader);
      Vue.component('pageTitleBar', PageTitleBar);
      Vue.component('rotateSquare2', RotateSquare2);
   }
}

export default GlobalComponents
