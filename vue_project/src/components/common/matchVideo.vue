<template>
   <video-player  ref="videoPlayer"
	             :options="playerOptions"
               :playsinline="true"
	             @play="onPlayerPlay($event)"
	             @pause="onPlayerPause($event)"
	             @ended="onPlayerEnded($event)"
	             @loadeddata="onPlayerLoadeddata($event)"
	             @waiting="onPlayerWaiting($event)"
	             @playing="onPlayerPlaying($event)"
	             @timeupdate="onPlayerTimeupdate($event)"
	             @canplay="onPlayerCanplay($event)"
	             @canplaythrough="onPlayerCanplaythrough($event)"
	             @ready="playerReadied"
	             @statechanged="playerStateChanged($event)">
	</video-player>
</template>

<script>
// import VueVideoPlayer from 'vue-video-player'
  export default {
    data() {
      return {
        playerOptions: {
          // component options
          start: 0,
          playsinline: false,
          // videojs options
          muted: true,
          language: 'zh-CN',
          playbackRates: [0.7, 1.0, 1.5, 2.0],
          sources: [{
            type: "video/mp4",
            src: ''
          }],
          height:175,
          autoplay:false,
        }
      }
    },
    props:['datas'],
    mounted() {
      // console.log('this is current player instance object', this.player)
      // setTimeout(() => {
        // console.log('dynamic change options', this)
        this.playerOptions.muted = false
      // }, 2000)

      this.setVideoUrl();

    },
    computed: {
      player() {
        return this.$refs.videoPlayer.player
      }
    },
    methods: {
      setVideoUrl(){
        this.playerOptions.sources[0].src = this.datas;
      },

      // listen event
      onPlayerPlay(player) {

        // console.log('player play!', player)
      },
      onPlayerPause(player) {
        // console.log('player pause!', player)
      },
      onPlayerEnded(player) {
        // console.log('player ended!', player)
      },
      onPlayerLoadeddata(player) {
        // console.log('player Loadeddata!', player)
      },
      onPlayerWaiting(player) {
        // console.log('player Waiting!', player)
      },
      onPlayerPlaying(player) {
        // console.log('player Playing!', player)
      },
      onPlayerTimeupdate(player) {
        // console.log('player Timeupdate!', player.currentTime())
      },
      onPlayerCanplay(player) {
        // console.log('player Canplay!', player)
      },
      onPlayerCanplaythrough(player) {
        // console.log('player Canplaythrough!', player)
      },
      // or listen state event
      playerStateChanged(playerCurrentState) {
        // console.log('player current update state', playerCurrentState)
      },
      // player is ready
      playerReadied(player) {
        // console.log('example 01: the player is readied', player)
      }
    }
  }
</script>