<template>
	<div class="recent-comment-widget" >
    <app-section-loader :status="loader"></app-section-loader>
		<vue-perfect-scrollbar style="height:360px" :settings="settings">
			<v-list three-line v-if="recentComments" class="list-aqua-ripple">
				<template v-for="comment in recentComments">
					<v-list-tile avatar :key="comment.id" ripple @click="openLink(comment.discussion.url)">
						<v-list-tile-avatar>
							<img :src="comment.head">
						</v-list-tile-avatar>
						<v-list-tile-content>
							<h6 class="mb-0">{{ comment.login }} в теме "{{ comment.discussion.title }}"</h6>
							<span class="fs-12">написал <a href="javascript:void(0)">{{ comment.time }}</a></span>
							<p class="fs-12 mb-0 fw-normal">{{ comment.body }}</p>
						</v-list-tile-content>
					</v-list-tile>
				</template>
			</v-list>
		</vue-perfect-scrollbar>
	</div>
</template>

<script>
import api from "Api";

export default {
  data() {
    return {
      loader: true,
      recentComments: null,
      settings: {
        maxScrollbarLength: 160
      }
    };
  },
  mounted() {
    this.getComments();
  },
  methods: {
    getComments() {
      api
        .post("dashboard/posts")
        .then(response => {
          this.recentComments = response.data;
          this.loader = false;
		})
        .catch(error => {
          console.log(error);
        });
    },
    openLink(link){
		window.open(link, "_blank");
	}
  }
};
</script>
