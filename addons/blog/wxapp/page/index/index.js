const { Tab } = require('../../assets/libs/zanui/index');

var app = getApp();
Page(Object.assign({}, Tab, {
  data: {
    bannerList: [],
    postList: [],
    loading: false,
    nodata: false,
    nomore: false,
    tab: {
      list: [],
      selectedId: '0',
      scroll: true,
      height: 44
    },
  },
  channel: 0,
  page: 1,
  onLoad: function () {
    var that = this;
    this.channel = 0;
    this.page = 1;
    this.setData({ ["tab.list"]: app.globalData.tabList });
    app.request('/index/index', {}, function (data, ret) {
      that.setData({
        bannerList: data.bannerList,
        postList: data.postList,
        ["tab.list"]: data.tabList
      });
    }, function (data, ret) {
      app.error(ret.msg);
    });
  },
  onPullDownRefresh: function () {
    this.setData({ nodata: false, nomore: false });
    this.page = 1;
    this.loadPost(function () {
      wx.stopPullDownRefresh();
    });
  },
  onReachBottom: function () {
    var that = this;
    this.loadPost(function (data) {
      if (data.postList.length == 0) {
        that.setData({ nomore: true, loading:false });
        app.info("暂无更多数据");
      }
    });
  },
  loadPost: function (cb) {
    var that = this;
    if (that.data.nomore == true || that.data.loading == true) {
      return;
    }
    this.setData({ loading: true });
    app.request('/post/index', { channel: this.channel, page: this.page }, function (data, ret) {
      that.setData({
        loading: false,
        nodata: that.page == 1 && data.postList.length == 0 ? true : false,
        nomore: that.page > 1 && data.postList.length == 0 ? true : false,
        postList: that.page > 1 ? that.data.postList.concat(data.postList) : data.postList,
      });
      that.page++;
      typeof cb == 'function' && cb(data);
    }, function (data, ret) {
      app.error(ret.msg);
    });
  },

  handleZanTabChange(e) {
    var componentId = e.componentId;
    var selectedId = e.selectedId;
    this.channel = selectedId;
    this.page = 1;
    this.setData({
      nodata: false,
      nomore: false,
      [`${componentId}.selectedId`]: selectedId
    });
    wx.pageScrollTo({ scrollTop: 0 });
    this.loadPost();
  },
  onShareAppMessage: function () {
    return {
      title: 'FastAdmin',
      desc: '基于ThinkPHP5和Bootstrap的极速后台框架',
      path: '/page/index/index'
    }
  }
}))