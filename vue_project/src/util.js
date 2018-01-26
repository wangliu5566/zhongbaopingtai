export default {
  install(Vue, options) {
    Vue.prototype.dateToSeconds = function(date) {
      let d = new Date(date);
      let formatDate = d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + d.getDate();
      let seconds = new Date(formatDate).getTime();
      return seconds;
    };
    Vue.prototype.secondsToDate = function(seconds) {
      let d = new Date(parseInt(seconds));
      return d;
    };
    Vue.prototype.secondsToNormalDate = function(seconds) {
      let d = new Date(seconds);
      let formatDate = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
      return formatDate;
    };
    Vue.prototype.scrollTop = function(el, from = 0, to, duration = 500) {
        if (!window.requestAnimationFrame) {
          window.requestAnimationFrame = (
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function(callback) {
              return window.setTimeout(callback, 1000 / 60);
            }
          );
        }
        const difference = Math.abs(from - to);
        const step = Math.ceil(difference / duration * 50);

        function scroll(start, end, step) {
          if (start === end) return;

          let d = (start + step > end) ? end : start + step;
          if (start > end) {
            d = (start - step < end) ? end : start - step;
          }
          if (el === window) {
            window.scrollTo(d, d);
          } else {
            el.scrollTop = d;
          }
          window.requestAnimationFrame(() => scroll(d, end, step));
        }
        scroll(from, to, step);
      },
      //更新本地筛选条件存储
      Vue.prototype.updateFilter = function(value) {
        let filters = window.sessionStorage.getItem('bg_user_filter');
        let newFilters;
        if (value != '') {
          if (filters != null && filters != '' && filters != {}) {
            newFilters = Object.assign({}, JSON.parse(filters), value)
            window.sessionStorage.setItem('bg_user_filter', JSON.stringify(newFilters));
          } else {
            window.sessionStorage.setItem('bg_user_filter', JSON.stringify(value));
          }
        }
      }
  }
}
