const { Toast } = require('../../assets/libs/zanui/index');

var app = getApp();

Page(Object.assign({}, Toast, {

  data: {
    userInfo: null,
    postInfo: { article: {} },
    commentList: [],
    loading: false,
    nodata: true,
    nomore: false,
    form: { quotepid: 0, message: '', focus: false }
  },
  page: 1,

  onLoad: function (options) {
    var that = this;
    that.setData({ userInfo: app.globalData.userInfo });
    app.request('/post/detail', { id: options.id }, function (data, ret) {
      var content = data.postInfo.content;
      data.postInfo.article = app.towxml.toJson(content, 'html');
      data.commentList.forEach(function (item) {
        item.article = app.towxml.toJson(item.content, 'html');
      });
      that.setData({ postInfo: data.postInfo, commentList: data.commentList, nodata: data.commentList.length === 0 });
      that.page++;
    }, function (data, ret) {
      app.error(ret.msg);
    });
  },
  reply: function (event) {
    var that = this;
    var pid = event.currentTarget.dataset.pid;
    var username = event.currentTarget.dataset.username;
    that.setData({ form: { quotepid: pid, message: '@' + username + ' ', focus: true } });
  },
  login: function (event) {
    var that = this;
    app.login(function (data) {
      app.info('登录成功');
      that.setData({ userInfo: app.globalData.userInfo });
    });
  },
  formSubmit: function (event) {
    var that = this;
    var pid = event.currentTarget.dataset.pid;
    if (!app.globalData.userInfo) {
      app.error('请登录后再评论');
      return;
    }
    if (event.detail.value.message == '') {
      app.error('内容不能为空');
      return;
    }
    app.request('/comment/post', { post_id: this.data.postInfo.id, pid: this.data.form.quotepid, username: app.globalData.userInfo.nickName, avatar: app.globalData.userInfo.avatarUrl, content: event.detail.value.content }, function (data, ret) {
      app.success(ret.msg);
      that.setData({ form: { quotepid: 0, message: '', focus: false }, commentList: [], nodata: false, nomore: false });
      if (that.data.commentList.length < 10) {
        that.page = 1;
      } else {
        that.data.commentList = that.data.commentList.slice(0, 10);
        that.page = 2;
      }
      that.onReachBottom();
    }, function (data, ret) {
      that.showZanToast(ret.msg);
    });
  },
  onReachBottom: function () {
    var that = this;
    this.loadComment(function (data) {
      if (data.commentList.length == 0) {
        //app.info("暂无更多数据");
      }
    });
  },
  loadComment: function (cb) {
    var that = this;
    if (that.data.nomore == true || that.data.loading == true) {
      return;
    }
    this.setData({ loading: true });
    app.request('/comment', { post_id: this.data.postInfo.id, page: this.page }, function (data, ret) {
      data.commentList.forEach(function (item) {
        item.article = app.towxml.toJson(item.content, 'html');
      });
      that.setData({
        loading: false,
        nodata: that.page == 1 && data.commentList.length == 0 ? true : false,
        nomore: that.page > 1 && data.commentList.length == 0 ? true : false,
        commentList: that.page > 1 ? that.data.commentList.concat(data.commentList) : data.commentList,
      });
      that.page++;
      typeof cb == 'function' && cb(data);
    }, function (data, ret) {
      that.setData({
        loading: false
      });
      app.error(ret.msg);
    });
  },

  share: function () {
    wx.showShareMenu({});
  },
  onShareAppMessage: function () {
    return {
      title: this.data.postInfo.title,
      desc: this.data.postInfo.intro,
      path: '/page/post/detail?id=' + this.data.postInfo.id
    }
  },
}))